<?php
require_once __DIR__ . '/imgrel.php';
require_once dirname(__DIR__, 2) . '/database/db_connect.php';

function syncStoreProductImages(): int
{
    $projectRoot = dirname(__DIR__, 2);
    $imageDirectory = $projectRoot . '/public/assets/images/products';
    $database = getAppDatabaseConnection();
    $games = $database->query(
        'SELECT game_id, steam_app_id, image_url FROM games WHERE steam_app_id IS NOT NULL'
    )->fetchAll();
    $updateImage = $database->prepare('UPDATE games SET image_url = :image_url WHERE game_id = :game_id');
    $updated = 0;

    foreach ($games as $game) {
        $image = 'steam_' . (int) $game['steam_app_id'] . '.jpg';
        if (!is_file($imageDirectory . '/' . $image) || (string) ($game['image_url'] ?? '') === $image) {
            continue;
        }
        $updateImage->execute([
            ':image_url' => $image,
            ':game_id' => (int) $game['game_id'],
        ]);
        $updated++;
    }

    return $updated;
}

function normalizeStoreProduct(array $item): ?array
{
    $id = trim((string) ($item['game_id'] ?? $item['id'] ?? ''));
    $name = trim((string) ($item['name'] ?? ''));
    if ($id === '' || $name === '') {
        return null;
    }

    $basePrice = max(0, (int) ($item['base_price'] ?? $item['price'] ?? 0));
    $discountRaw = $item['discount_check'] ?? 0;
    $discount = is_numeric($discountRaw) ? (float) $discountRaw : 0.0;
    $discount = min(100.0, max(0.0, $discount));
    $calculatedPrice = (int) round($basePrice * (1 - ($discount / 100)));
    $finalPriceRaw = $item['final_price'] ?? null;
    $finalPrice = is_numeric($finalPriceRaw)
        ? max(0, (int) round((float) $finalPriceRaw))
        : $calculatedPrice;

    $rawTags = $item['tags'] ?? [];
    if (is_string($rawTags)) {
        $rawTags = explode(',', $rawTags);
    }
    $tags = [];
    if (is_array($rawTags)) {
        foreach ($rawTags as $tag) {
            if (!is_scalar($tag)) {
                continue;
            }
            $tag = trim((string) $tag);
            if ($tag !== '' && !in_array($tag, $tags, true)) {
                $tags[] = $tag;
            }
        }
    }

    $reviews = [];
    $rawReviews = is_array($item['reviews'] ?? null) ? $item['reviews'] : [];
    foreach ($rawReviews as $review) {
        $reviewRating = is_array($review) ? ($review['rating'] ?? null) : $review;
        if (!is_numeric($reviewRating)) {
            continue;
        }
        $reviewRating = (float) $reviewRating;
        if ($reviewRating < 0 || $reviewRating > 5) {
            continue;
        }
        $reviews[] = ['rating' => $reviewRating];
    }

    $rating = null;
    if ($reviews !== []) {
        $rating = round(
            array_sum(array_column($reviews, 'rating')) / count($reviews),
            1
        );
    }

    return [
        'game_id'        => $id,
        'id'             => $id,
        'name'           => $name,
        'summary'        => trim((string) ($item['summary'] ?? '')),
        'description'    => trim((string) ($item['description'] ?? ($item['summary'] ?? ''))),
        'base_price'     => $basePrice,
        'discount_check' => $discount,
        'final_price'    => $finalPrice,
        'price'          => $finalPrice,
        'tags'           => $tags,
        'reviews'        => $reviews,
        'rating'         => $rating,
        'review_count'   => count($reviews),
        'image'          => trim((string) ($item['image'] ?? $item['image_url'] ?? '')),
    ];
}

function saveStoreProducts(array $products): bool
{
    $normalized = [];
    foreach ($products as $item) {
        if (!is_array($item)) {
            return false;
        }
        $product = normalizeStoreProduct($item);
        if ($product === null || !ctype_digit($product['game_id'])) {
            return false;
        }
        $normalized[] = $product;
    }

    $database = getAppDatabaseConnection();
    $existingIds = array_map(
        'strval',
        $database->query('SELECT game_id FROM games')->fetchAll(PDO::FETCH_COLUMN)
    );
    $incomingIds = [];
    $upsertGame = $database->prepare(
        'INSERT INTO games (game_id, name, description, image_url, base_price, final_price, discount_check)
         VALUES (:game_id, :name, :description, :image_url, :base_price, :final_price, :discount_check)
         ON CONFLICT(game_id) DO UPDATE SET
           name = excluded.name,
           description = excluded.description,
           image_url = excluded.image_url,
           base_price = excluded.base_price,
           final_price = excluded.final_price,
           discount_check = excluded.discount_check'
    );
    $deleteTags = $database->prepare('DELETE FROM game_tags WHERE game_id = :game_id');
    $insertTag = $database->prepare('INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (:game_id, :tag)');
    $deleteGame = $database->prepare('DELETE FROM games WHERE game_id = :game_id');

    $database->beginTransaction();
    try {
        foreach ($normalized as $product) {
            $gameId = (int) $product['game_id'];
            $basePrice = (int) $product['base_price'];
            $finalPrice = (int) $product['final_price'];
            $discountFlag = $product['discount_check'] > 0 && $finalPrice < $basePrice ? 1 : 0;

            $upsertGame->execute([
                ':game_id' => $gameId,
                ':name' => $product['name'],
                ':description' => $product['description'],
                ':image_url' => $product['image'],
                ':base_price' => $basePrice,
                ':final_price' => $finalPrice,
                ':discount_check' => $discountFlag,
            ]);

            $deleteTags->execute([':game_id' => $gameId]);
            foreach ($product['tags'] as $tag) {
                $insertTag->execute([
                    ':game_id' => $gameId,
                    ':tag' => $tag,
                ]);
            }
            $incomingIds[$product['game_id']] = true;
        }

        foreach ($existingIds as $existingId) {
            if (!isset($incomingIds[$existingId])) {
                $deleteGame->execute([':game_id' => (int) $existingId]);
            }
        }

        $database->commit();
        return true;
    } catch (PDOException $error) {
        if ($database->inTransaction()) {
            $database->rollBack();
        }
        error_log('Unable to save game catalog: ' . $error->getMessage());
        return false;
    }
}

function getStoreProducts(array $incomingProducts = []): array
{
    if (!empty($incomingProducts)) {
        $normalized = [];
        foreach ($incomingProducts as $item) {
            if (is_array($item)) {
                $product = normalizeStoreProduct($item);
                if ($product !== null) {
                    $normalized[] = $product;
                }
            }
        }
        return $normalized;
    }

    $database = getAppDatabaseConnection();
    $gameRows = $database->query(
        'SELECT game_id, steam_app_id, name, description, image_url, base_price, final_price,
                discount_check
         FROM games
         ORDER BY game_id'
    )->fetchAll();

    $tagsByGameId = [];
    foreach ($database->query('SELECT game_id, tag FROM game_tags ORDER BY game_id, tag')->fetchAll() as $row) {
        $tagsByGameId[(string) $row['game_id']][] = (string) $row['tag'];
    }

    $reviewsByGameId = [];
    foreach ($database->query('SELECT game_id, rating_score FROM reviews ORDER BY review_id')->fetchAll() as $row) {
        $reviewsByGameId[(string) $row['game_id']][] = (int) $row['rating_score'];
    }

    $normalized = [];
    foreach ($gameRows as $row) {
        $gameId = (string) $row['game_id'];
        $basePrice = (float) $row['base_price'];
        $finalPrice = (float) $row['final_price'];
        $discount = (int) $row['discount_check'] === 1 && $basePrice > 0 && $finalPrice < $basePrice
            ? round((($basePrice - $finalPrice) / $basePrice) * 100, 2)
            : 0;
        $image = trim((string) ($row['image_url'] ?? ''));
        if ($image === '' && !empty($row['steam_app_id'])) {
            $image = 'steam_' . (int) $row['steam_app_id'] . '.jpg';
        }

        $item = [
            'game_id' => $gameId,
            'name' => (string) $row['name'],
            'summary' => (string) ($row['description'] ?? ''),
            'description' => (string) ($row['description'] ?? ''),
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'discount_check' => $discount,
            'tags' => $tagsByGameId[$gameId] ?? [],
            'reviews' => $reviewsByGameId[$gameId] ?? [],
            'image' => $image,
        ];
        $product = normalizeStoreProduct($item);
        if ($product !== null) {
            $normalized[] = $product;
        }
    }
    return $normalized;
}

function findStoreProductById(array $products, string $id): ?array
{
    foreach ($products as $item) {
        if ((string) ($item['game_id'] ?? $item['id'] ?? '') === $id) {
            return $item;
        }
    }
    return null;
}

function generateNextProductId(array $products): string
{
    $maxNum = 0;
    foreach ($products as $item) {
        $id = (string) ($item['game_id'] ?? $item['id'] ?? '');
        if (ctype_digit($id)) {
            $maxNum = max($maxNum, (int) $id);
        }
    }
    return (string) ($maxNum + 1);
}

function isAdminUser(mixed $user = null): bool
{
    if (!empty($_SESSION['force_user'])) {
        return false;
    }

    $candidate = $user ?? ($_SESSION['user'] ?? null);
    if (empty($candidate)) {
        return false;
    }

    if (is_array($candidate)) {
        $role = strtolower((string) ($candidate['role'] ?? ''));
        $username = strtolower((string) ($candidate['username'] ?? ''));
        $name = strtolower((string) ($candidate['name'] ?? ''));
        return $role === 'admin' || $username === 'admin' || $name === 'admin';
    }

    return strtolower((string) $candidate) === 'admin';
}