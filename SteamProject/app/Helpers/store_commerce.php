<?php
require_once __DIR__ . '/../../database/db_connect.php';
require_once __DIR__ . '/imgrel.php';

function ensureStoreCommerceTables(): void
{
    static $initialized = false;
    if ($initialized) {
        return;
    }

    $database = getAppDatabaseConnection();
    $database->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS store_orders (
            order_id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_key TEXT NOT NULL,
            customer_name TEXT NOT NULL DEFAULT 'Guest',
            status TEXT NOT NULL DEFAULT 'pending'
                CHECK (status IN ('pending', 'processing', 'completed', 'cancelled')),
            total_price REAL NOT NULL DEFAULT 0 CHECK (total_price >= 0),
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS store_carts (
            cart_id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_key TEXT NOT NULL,
            customer_name TEXT NOT NULL DEFAULT 'Guest',
            status TEXT NOT NULL DEFAULT 'active'
                CHECK (status IN ('active', 'abandoned', 'converted')),
            converted_order_id INTEGER,
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now')),
            FOREIGN KEY (converted_order_id) REFERENCES store_orders(order_id)
                ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS store_cart_items (
            cart_id INTEGER NOT NULL,
            game_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL CHECK (quantity > 0),
            PRIMARY KEY (cart_id, game_id),
            FOREIGN KEY (cart_id) REFERENCES store_carts(cart_id)
                ON DELETE CASCADE,
            FOREIGN KEY (game_id) REFERENCES games(game_id)
                ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS store_order_items (
            order_item_id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            game_id INTEGER NOT NULL,
            game_name TEXT NOT NULL,
            unit_price REAL NOT NULL CHECK (unit_price >= 0),
            quantity INTEGER NOT NULL CHECK (quantity > 0),
            line_total REAL NOT NULL CHECK (line_total >= 0),
            FOREIGN KEY (order_id) REFERENCES store_orders(order_id)
                ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_store_carts_session_status
            ON store_carts(session_key, status, cart_id);
        CREATE INDEX IF NOT EXISTS idx_store_carts_status_updated
            ON store_carts(status, updated_at);
        CREATE INDEX IF NOT EXISTS idx_store_orders_status_created
            ON store_orders(status, created_at);
        CREATE INDEX IF NOT EXISTS idx_store_order_items_order
            ON store_order_items(order_id);
        SQL);
    $initialized = true;
}

function normalizeStoreCartItems(array $items): array
{
    $normalized = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            throw new InvalidArgumentException('Dữ liệu sản phẩm trong giỏ hàng không hợp lệ.');
        }

        $gameId = filter_var($item['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $quantity = filter_var($item['qty'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
        if ($gameId === false || $quantity === false) {
            throw new InvalidArgumentException('Mã game hoặc số lượng sản phẩm không hợp lệ.');
        }

        $normalized[$gameId] = ($normalized[$gameId] ?? 0) + $quantity;
        if ($normalized[$gameId] > 99) {
            throw new InvalidArgumentException('Mỗi sản phẩm chỉ được đặt tối đa 99 bản.');
        }
    }

    return $normalized;
}

function getStoreCartForSession(string $sessionKey): array
{
    ensureStoreCommerceTables();
    $database = getAppDatabaseConnection();
    $statement = $database->prepare(
        'SELECT cart_id, customer_name, status, converted_order_id
         FROM store_carts WHERE session_key = :session_key
         ORDER BY cart_id DESC LIMIT 1'
    );
    $statement->execute([':session_key' => $sessionKey]);
    $cart = $statement->fetch();
    if (!$cart) {
        return ['cart_id' => null, 'status' => null, 'items' => [], 'total' => 0];
    }
    if ($cart['status'] === 'converted') {
        return [
            'cart_id' => (int) $cart['cart_id'],
            'customer_name' => (string) $cart['customer_name'],
            'status' => 'converted',
            'converted_order_id' => (int) $cart['converted_order_id'],
            'items' => [],
            'total' => 0,
        ];
    }

    $itemStatement = $database->prepare(
        'SELECT i.game_id AS id, i.quantity AS qty, g.name, g.final_price AS price, g.image_url
         FROM store_cart_items i
         JOIN games g ON g.game_id = i.game_id
         WHERE i.cart_id = :cart_id
         ORDER BY g.name'
    );
    $itemStatement->execute([':cart_id' => $cart['cart_id']]);
    $items = $itemStatement->fetchAll();
    $total = 0;
    foreach ($items as &$item) {
        $item['id'] = (string) $item['id'];
        $item['qty'] = (int) $item['qty'];
        $item['price'] = (int) round((float) $item['price']);
        $item['image'] = resolveProductImage((string) ($item['image_url'] ?? ''), $item['id']);
        unset($item['image_url']);
        $total += $item['price'] * $item['qty'];
    }
    unset($item);

    return [
        'cart_id' => (int) $cart['cart_id'],
        'customer_name' => (string) $cart['customer_name'],
        'status' => (string) $cart['status'],
        'converted_order_id' => $cart['converted_order_id'] === null ? null : (int) $cart['converted_order_id'],
        'items' => $items,
        'total' => $total,
    ];
}

function syncStoreCart(string $sessionKey, string $customerName, array $items): array
{
    ensureStoreCommerceTables();
    $normalizedItems = normalizeStoreCartItems($items);
    $database = getAppDatabaseConnection();

    if ($normalizedItems === []) {
        $existing = getStoreCartForSession($sessionKey);
        if ($existing['cart_id'] === null) {
            return $existing;
        }
    }

    $database->beginTransaction();
    try {
        $statement = $database->prepare(
            'SELECT cart_id, status FROM store_carts
             WHERE session_key = :session_key
             ORDER BY cart_id DESC LIMIT 1'
        );
        $statement->execute([':session_key' => $sessionKey]);
        $cart = $statement->fetch();
        if ($cart && $cart['status'] === 'converted') {
            $cart = false;
        }

        if (!$cart && $normalizedItems === []) {
            $database->commit();
            return ['cart_id' => null, 'status' => null, 'items' => [], 'total' => 0];
        }

        if ($cart) {
            if ($cart['status'] === 'abandoned' && $normalizedItems !== []) {
                $database->prepare(
                    "UPDATE store_carts SET status = 'active', customer_name = :customer_name,
                     updated_at = datetime('now') WHERE cart_id = :cart_id"
                )->execute([
                    ':customer_name' => $customerName,
                    ':cart_id' => $cart['cart_id'],
                ]);
            } elseif ($cart['status'] === 'active' && $normalizedItems === []) {
                $database->prepare(
                    "UPDATE store_carts SET status = 'abandoned', customer_name = :customer_name,
                     updated_at = datetime('now') WHERE cart_id = :cart_id"
                )->execute([
                    ':customer_name' => $customerName,
                    ':cart_id' => $cart['cart_id'],
                ]);
            } else {
                $database->prepare(
                    "UPDATE store_carts SET customer_name = :customer_name, updated_at = datetime('now')
                     WHERE cart_id = :cart_id"
                )->execute([
                    ':customer_name' => $customerName,
                    ':cart_id' => $cart['cart_id'],
                ]);
            }
            $cartId = (int) $cart['cart_id'];
        } else {
            $database->prepare(
                "INSERT INTO store_carts (session_key, customer_name, status)
                 VALUES (:session_key, :customer_name, 'active')"
            )->execute([
                ':session_key' => $sessionKey,
                ':customer_name' => $customerName,
            ]);
            $cartId = (int) $database->lastInsertId();
        }

        $deleteItems = $database->prepare('DELETE FROM store_cart_items WHERE cart_id = :cart_id');
        $deleteItems->execute([':cart_id' => $cartId]);
        $insertItem = $database->prepare(
            'INSERT INTO store_cart_items (cart_id, game_id, quantity)
             SELECT :cart_id, game_id, :quantity FROM games WHERE game_id = :game_id'
        );

        foreach ($normalizedItems as $gameId => $quantity) {
            $insertItem->execute([
                ':cart_id' => $cartId,
                ':quantity' => $quantity,
                ':game_id' => $gameId,
            ]);
            if ($insertItem->rowCount() !== 1) {
                throw new DomainException('Một sản phẩm trong giỏ không còn tồn tại trong cửa hàng.');
            }
        }

        $database->commit();
        return getStoreCartForSession($sessionKey);
    } catch (Throwable $error) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        throw $error;
    }
}

function createStoreOrderFromCart(string $sessionKey, string $customerName): int
{
    ensureStoreCommerceTables();
    $database = getAppDatabaseConnection();
    $database->beginTransaction();

    try {
        $cartStatement = $database->prepare(
            "SELECT cart_id FROM store_carts
             WHERE session_key = :session_key AND status = 'active'
             ORDER BY cart_id DESC LIMIT 1"
        );
        $cartStatement->execute([':session_key' => $sessionKey]);
        $cartId = $cartStatement->fetchColumn();
        if ($cartId === false) {
            throw new DomainException('Giỏ hàng đang trống hoặc chưa được đồng bộ.');
        }

        $itemsStatement = $database->prepare(
            'SELECT i.game_id, i.quantity, g.name, g.final_price
             FROM store_cart_items i
             JOIN games g ON g.game_id = i.game_id
             WHERE i.cart_id = :cart_id'
        );
        $itemsStatement->execute([':cart_id' => $cartId]);
        $items = $itemsStatement->fetchAll();
        if ($items === []) {
            throw new DomainException('Không thể thanh toán một giỏ hàng đang trống.');
        }

        $total = 0;
        foreach ($items as $item) {
            $total += (int) round((float) $item['final_price']) * (int) $item['quantity'];
        }

        $database->prepare(
            "INSERT INTO store_orders (session_key, customer_name, status, total_price)
             VALUES (:session_key, :customer_name, 'pending', :total_price)"
        )->execute([
            ':session_key' => $sessionKey,
            ':customer_name' => $customerName,
            ':total_price' => $total,
        ]);
        $orderId = (int) $database->lastInsertId();
        $insertOrderItem = $database->prepare(
            'INSERT INTO store_order_items
                (order_id, game_id, game_name, unit_price, quantity, line_total)
             VALUES (:order_id, :game_id, :game_name, :unit_price, :quantity, :line_total)'
        );

        foreach ($items as $item) {
            $unitPrice = (int) round((float) $item['final_price']);
            $quantity = (int) $item['quantity'];
            $insertOrderItem->execute([
                ':order_id' => $orderId,
                ':game_id' => (int) $item['game_id'],
                ':game_name' => (string) $item['name'],
                ':unit_price' => $unitPrice,
                ':quantity' => $quantity,
                ':line_total' => $unitPrice * $quantity,
            ]);
        }

        $database->prepare(
            "UPDATE store_carts SET status = 'converted', converted_order_id = :order_id,
             updated_at = datetime('now') WHERE cart_id = :cart_id"
        )->execute([
            ':order_id' => $orderId,
            ':cart_id' => $cartId,
        ]);

        $database->commit();
        return $orderId;
    } catch (Throwable $error) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        throw $error;
    }
}

function getAdminStoreCarts(string $search = '', string $status = ''): array
{
    ensureStoreCommerceTables();
    $database = getAppDatabaseConnection();
    $conditions = [];
    $parameters = [];
    if (in_array($status, ['active', 'abandoned', 'converted'], true)) {
        $conditions[] = 'c.status = :status';
        $parameters[':status'] = $status;
    }
    if ($search !== '') {
        $conditions[] = "(CAST(c.cart_id AS TEXT) LIKE :search
            OR c.customer_name LIKE :search
            OR c.session_key LIKE :search
            OR EXISTS (
                SELECT 1 FROM store_cart_items si JOIN games sg ON sg.game_id = si.game_id
                WHERE si.cart_id = c.cart_id AND sg.name LIKE :search
            ))";
        $parameters[':search'] = '%' . $search . '%';
    }

    $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
    $statement = $database->prepare(
        "SELECT c.cart_id, c.session_key, c.customer_name, c.status, c.converted_order_id,
                c.created_at, c.updated_at, COUNT(i.game_id) AS item_count,
                COALESCE(SUM(i.quantity * g.final_price), 0) AS total_price
         FROM store_carts c
         LEFT JOIN store_cart_items i ON i.cart_id = c.cart_id
         LEFT JOIN games g ON g.game_id = i.game_id
         $where
         GROUP BY c.cart_id
         ORDER BY c.updated_at DESC, c.cart_id DESC"
    );
    $statement->execute($parameters);
    $carts = $statement->fetchAll();
    foreach ($carts as &$cart) {
        $cart['cart_id'] = (int) $cart['cart_id'];
        $cart['item_count'] = (int) $cart['item_count'];
        $cart['total_price'] = (int) round((float) $cart['total_price']);
        $cart['items'] = getStoreCartItemsById($database, $cart['cart_id']);
    }
    unset($cart);

    return $carts;
}

function getStoreCartItemsById(PDO $database, int $cartId): array
{
    $statement = $database->prepare(
        'SELECT i.game_id, i.quantity, g.name, g.final_price
         FROM store_cart_items i
         LEFT JOIN games g ON g.game_id = i.game_id
         WHERE i.cart_id = :cart_id
         ORDER BY g.name, i.game_id'
    );
    $statement->execute([':cart_id' => $cartId]);
    return $statement->fetchAll();
}

function getAdminStoreOrders(string $search = '', string $status = ''): array
{
    ensureStoreCommerceTables();
    $database = getAppDatabaseConnection();
    $conditions = [];
    $parameters = [];
    if (in_array($status, ['pending', 'processing', 'completed', 'cancelled'], true)) {
        $conditions[] = 'o.status = :status';
        $parameters[':status'] = $status;
    }
    if ($search !== '') {
        $conditions[] = "(CAST(o.order_id AS TEXT) LIKE :search
            OR o.customer_name LIKE :search
            OR o.session_key LIKE :search
            OR EXISTS (
                SELECT 1 FROM store_order_items si
                WHERE si.order_id = o.order_id AND si.game_name LIKE :search
            ))";
        $parameters[':search'] = '%' . $search . '%';
    }

    $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
    $statement = $database->prepare(
        "SELECT o.order_id, o.session_key, o.customer_name, o.status, o.total_price,
                o.created_at, o.updated_at, COUNT(i.order_item_id) AS item_count
         FROM store_orders o
         LEFT JOIN store_order_items i ON i.order_id = o.order_id
         $where
         GROUP BY o.order_id
         ORDER BY o.created_at DESC, o.order_id DESC"
    );
    $statement->execute($parameters);
    $orders = $statement->fetchAll();
    foreach ($orders as &$order) {
        $order['order_id'] = (int) $order['order_id'];
        $order['item_count'] = (int) $order['item_count'];
        $order['total_price'] = (int) round((float) $order['total_price']);
        $order['items'] = getStoreOrderItemsById($database, $order['order_id']);
    }
    unset($order);

    return $orders;
}

function getStoreOrderItemsById(PDO $database, int $orderId): array
{
    $statement = $database->prepare(
        'SELECT game_id, game_name, unit_price, quantity, line_total
         FROM store_order_items
         WHERE order_id = :order_id
         ORDER BY game_name, game_id'
    );
    $statement->execute([':order_id' => $orderId]);
    return $statement->fetchAll();
}

function updateAdminStoreCartStatus(int $cartId, string $status): bool
{
    if ($cartId < 1 || !in_array($status, ['active', 'abandoned'], true)) {
        throw new InvalidArgumentException('Trạng thái giỏ hàng không hợp lệ.');
    }

    ensureStoreCommerceTables();
    $statement = getAppDatabaseConnection()->prepare(
        "UPDATE store_carts SET status = :status, updated_at = datetime('now')
         WHERE cart_id = :cart_id AND status != 'converted'"
    );
    $statement->execute([':status' => $status, ':cart_id' => $cartId]);
    return $statement->rowCount() > 0;
}

function updateAdminStoreOrderStatus(int $orderId, string $status): bool
{
    if ($orderId < 1 || !in_array($status, ['pending', 'processing', 'completed', 'cancelled'], true)) {
        throw new InvalidArgumentException('Trạng thái đơn hàng không hợp lệ.');
    }

    ensureStoreCommerceTables();
    $statement = getAppDatabaseConnection()->prepare(
        "UPDATE store_orders SET status = :status, updated_at = datetime('now')
         WHERE order_id = :order_id"
    );
    $statement->execute([':status' => $status, ':order_id' => $orderId]);
    return $statement->rowCount() > 0;
}
