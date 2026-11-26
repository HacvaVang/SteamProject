<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';
require_once __DIR__ . '/../../../Helpers/store_commerce.php';

$styles = [
    '/public/assets/css/product.css',
    '/public/assets/css/admin-products.css',
];
$scripts = ['/public/assets/js/catalog-search.js'];
$sessionUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin = isAdminUser($sessionUser);
$adminNotice = '';
$adminError = '';
$tab = (string) ($_GET['tab'] ?? $_POST['tab'] ?? 'carts');
$tab = in_array($tab, ['carts', 'orders'], true) ? $tab : 'carts';

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordType = (string) ($_POST['record_type'] ?? '');
    $recordId = filter_var($_POST['record_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $status = (string) ($_POST['status'] ?? '');

    try {
        if ($recordId === false) {
            throw new InvalidArgumentException('Mã giỏ hàng hoặc đơn hàng không hợp lệ.');
        }

        if ($recordType === 'cart') {
            $updated = updateAdminStoreCartStatus($recordId, $status);
            $adminNotice = $updated ? 'Đã cập nhật trạng thái giỏ hàng.' : 'Không tìm thấy giỏ hàng có thể cập nhật.';
        } elseif ($recordType === 'order') {
            $updated = updateAdminStoreOrderStatus($recordId, $status);
            $adminNotice = $updated ? 'Đã cập nhật trạng thái đơn hàng.' : 'Không tìm thấy đơn hàng.';
        } else {
            throw new InvalidArgumentException('Loại dữ liệu cần cập nhật không hợp lệ.');
        }
    } catch (InvalidArgumentException $error) {
        $adminError = $error->getMessage();
    }
}

$search = trim((string) ($_GET['q'] ?? ''));
$searchEscaped = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
$statusFilter = trim((string) ($_GET['status'] ?? ''));
$cartStatuses = [
    'active' => 'Đang hoạt động',
    'abandoned' => 'Bỏ dở',
    'converted' => 'Đã chuyển thành đơn',
];
$orderStatuses = [
    'pending' => 'Chờ xử lý',
    'processing' => 'Đang xử lý',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy',
];

$allCarts = $isAdmin ? getAdminStoreCarts() : [];
$allOrders = $isAdmin ? getAdminStoreOrders() : [];
$carts = $isAdmin && $tab === 'carts'
    ? getAdminStoreCarts($search, $statusFilter)
    : [];
$orders = $isAdmin && $tab === 'orders'
    ? getAdminStoreOrders($search, $statusFilter)
    : [];
$activeCartCount = count(array_filter($allCarts, static fn(array $cart): bool => $cart['status'] === 'active'));
$pendingOrderCount = count(array_filter($allOrders, static fn(array $order): bool => $order['status'] === 'pending'));
?>
<section class="adminProductsWrap adminCommerceWrap" aria-label="Quản lý giỏ hàng và đơn hàng">
  <aside class="adminSidebar" aria-label="Điều hướng quản trị">
    <a class="adminBrand" href="/admin/products">
      <span class="adminBrandMark" aria-hidden="true">S</span>
      <span class="adminBrandText"><strong>Steam</strong><small>ADMIN PANEL</small></span>
    </a>
    <p class="adminNavLabel">MENU</p>
    <nav class="adminSidebarNav">
      <a class="adminNavLink" href="/admin/products">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4zM14 4h6v4h-6zM14 11h6v9h-6zM4 14h7v6H4z"/></svg>
        <span>Sản phẩm</span>
      </a>
      <a class="adminNavLink" href="/admin/qa">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v11H9l-5 4zM8 9h8M8 12h5"/></svg>
        <span>Q&amp;A</span>
      </a>
      <a class="adminNavLink is-active" href="/admin/orders" aria-current="page">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v15H4zM8 9h8M8 13h8M8 17h4"/></svg>
        <span>Giỏ hàng &amp; đơn hàng</span>
      </a>
      <a class="adminNavLink" href="/admin/faqs">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M9.8 9a2.3 2.3 0 1 1 3.8 1.7c-1 .8-1.6 1.2-1.6 2.8M12 17h.01"/></svg>
        <span>FAQ</span>
      </a>
    </nav>
    <div class="adminSidebarBottom">
      <a class="adminNavLink" href="/products">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11 12 4l9 7M5 10v10h14V10M9 20v-6h6v6"/></svg>
        <span>Quay lại Store</span>
      </a>
      <div class="adminSidebarUser">
        <span class="adminAvatar" aria-hidden="true">A</span>
        <span><strong><?= $isAdmin ? 'Administrator' : 'Guest' ?></strong><small><?= $isAdmin ? 'Quản trị viên' : 'Chưa có quyền Admin' ?></small></span>
      </div>
    </div>
  </aside>

  <div class="adminDashboardMain">
    <header class="adminTopbar">
      <div>
        <p class="adminBreadcrumb">Admin <span>/</span> Commerce</p>
        <h1>Giỏ hàng &amp; đơn hàng</h1>
      </div>
      <a href="/admin/products" class="adminButton adminButtonSecondary">Quản lý sản phẩm</a>
    </header>

    <div class="adminDashboardContent">
      <?php if (!$isAdmin): ?>
        <section class="adminLockedBox" aria-label="Yêu cầu quyền Admin">
          <span class="adminLockIcon" aria-hidden="true">!</span>
          <div>
            <h2>Truy cập bị hạn chế</h2>
            <p>Trang quản lý giỏ hàng và đơn hàng chỉ dành cho tài khoản có quyền Admin.</p>
            <div class="adminLockedActions">
              <a href="/login" class="adminButton adminButtonPrimary">Đăng nhập tài khoản Admin</a>
              <a href="/products" class="adminButton adminButtonSecondary">Quay lại Store</a>
            </div>
          </div>
        </section>
      <?php else: ?>
        <?php if ($adminNotice !== ''): ?>
          <div class="adminAlertSuccess" role="status"><?= htmlspecialchars($adminNotice, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($adminError !== ''): ?>
          <div class="adminAlertError" role="alert"><?= htmlspecialchars($adminError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <section class="adminStats" aria-label="Thống kê giao dịch">
          <article class="adminStatCard">
            <span class="adminStatIcon adminStatIconBlue" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M4 5h16v15H4zM8 9h8M8 13h8"/></svg>
            </span>
            <div><span>Tổng giỏ hàng</span><strong><?= count($allCarts) ?></strong><small>Đang lưu trong hệ thống</small></div>
          </article>
          <article class="adminStatCard">
            <span class="adminStatIcon adminStatIconGreen" aria-hidden="true">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="m8 12 2.5 2.5L16 9"/></svg>
            </span>
            <div><span>Giỏ đang hoạt động</span><strong><?= $activeCartCount ?></strong><small>Chưa checkout</small></div>
          </article>
          <article class="adminStatCard">
            <span class="adminStatIcon adminStatIconOrange" aria-hidden="true">
              <svg viewBox="0 0 24 24"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg>
            </span>
            <div><span>Tổng đơn hàng</span><strong><?= count($allOrders) ?></strong><small>Checkout đã tạo</small></div>
          </article>
          <article class="adminStatCard">
            <span class="adminStatIcon adminStatIconPurple" aria-hidden="true">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div><span>Đơn chờ xử lý</span><strong><?= $pendingOrderCount ?></strong><small>Cần cập nhật trạng thái</small></div>
          </article>
        </section>

        <section class="adminCatalogPanel catalogSection" aria-label="Danh sách giỏ hàng và đơn hàng" data-page-size="12">
          <nav class="adminCommerceTabs" aria-label="Loại dữ liệu">
            <a href="/admin/orders?tab=carts" class="<?= $tab === 'carts' ? 'is-active' : '' ?>" <?= $tab === 'carts' ? 'aria-current="page"' : '' ?>>
              Giỏ hàng <span><?= count($allCarts) ?></span>
            </a>
            <a href="/admin/orders?tab=orders" class="<?= $tab === 'orders' ? 'is-active' : '' ?>" <?= $tab === 'orders' ? 'aria-current="page"' : '' ?>>
              Đơn hàng <span><?= count($allOrders) ?></span>
            </a>
          </nav>

          <div class="adminPanelHeader adminCommerceHeader">
            <div>
              <h2 class="adminFormHeading"><?= $tab === 'carts' ? 'Giỏ hàng khách hàng' : 'Đơn hàng đã checkout' ?></h2>
              <p><?= $tab === 'carts' ? 'Xem nội dung giỏ và cập nhật trạng thái hoạt động.' : 'Theo dõi chi tiết đơn và cập nhật tiến độ xử lý.' ?></p>
            </div>
          </div>

          <form method="GET" action="/admin/orders" class="adminSearchForm" role="search">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab, ENT_QUOTES, 'UTF-8') ?>">
            <label class="adminSearchBox">
              <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m15.5 15.5 4.2 4.2"/></svg>
              <input
                type="search"
                name="q"
                value="<?= $searchEscaped ?>"
                class="adminSearchInput"
                data-catalog-search
                placeholder="Tìm mã, khách hàng, game..."
                aria-label="Tìm trong danh sách"
              >
            </label>
            <select class="adminStatusFilter" name="status" aria-label="Lọc trạng thái">
              <option value="">Tất cả trạng thái</option>
              <?php foreach (($tab === 'carts' ? $cartStatuses : $orderStatuses) as $statusValue => $statusLabel): ?>
                <option value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>" <?= $statusFilter === $statusValue ? 'selected' : '' ?>>
                  <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="adminButton adminButtonSecondary">Lọc</button>
          </form>

          <?php if ($tab === 'carts'): ?>
            <?php if ($carts === []): ?>
              <div class="adminEmptyState">Chưa có giỏ hàng phù hợp.</div>
            <?php else: ?>
              <div class="adminTableWrap">
                <table class="adminTable adminCommerceTable">
                  <thead>
                    <tr><th scope="col">Giỏ hàng</th><th scope="col">Sản phẩm</th><th scope="col">Tổng tạm tính</th><th scope="col">Trạng thái</th><th scope="col">Cập nhật</th><th scope="col"><span class="visuallyHidden">Thao tác</span></th></tr>
                  </thead>
                  <tbody data-catalog-list>
                    <?php foreach ($carts as $cart): ?>
                      <?php
                      $cartId = (int) $cart['cart_id'];
                      $cartItemSummary = implode(' ', array_map(
                          static fn(array $item): string => (string) ($item['name'] ?? 'Sản phẩm'),
                          $cart['items']
                      ));
                      $searchText = htmlspecialchars(implode(' ', [
                          (string) $cartId,
                          (string) $cart['customer_name'],
                          substr((string) $cart['session_key'], 0, 8),
                          (string) $cart['status'],
                          $cartItemSummary,
                      ]), ENT_QUOTES, 'UTF-8');
                      ?>
                      <tr class="adminProductRow" data-catalog-item data-search="<?= $searchText ?>">
                        <td>
                          <div class="adminRecordIdentity">
                            <strong>#<?= $cartId ?></strong>
                            <small><?= htmlspecialchars((string) $cart['customer_name'], ENT_QUOTES, 'UTF-8') ?></small>
                            <small>Phiên <?= htmlspecialchars(substr((string) $cart['session_key'], 0, 8), ENT_QUOTES, 'UTF-8') ?></small>
                          </div>
                        </td>
                        <td>
                          <details class="adminRecordDetails">
                            <summary><?= (int) $cart['item_count'] ?> sản phẩm</summary>
                            <ul>
                              <?php foreach ($cart['items'] as $item): ?>
                                <li>
                                  <?= htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm đã xóa'), ENT_QUOTES, 'UTF-8') ?>
                                  <span>× <?= (int) $item['quantity'] ?></span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          </details>
                        </td>
                        <td><strong class="adminPrice"><?= number_format((int) $cart['total_price'], 0, ',', '.') ?> đ</strong></td>
                        <td><span class="adminStatusPill is-<?= htmlspecialchars((string) $cart['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($cartStatuses[$cart['status']] ?? (string) $cart['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) $cart['updated_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                          <?php if ($cart['status'] === 'converted'): ?>
                            <a class="adminButton adminButtonSmall adminButtonSecondary" href="/admin/orders?tab=orders&amp;q=<?= (int) $cart['converted_order_id'] ?>">Đơn #<?= (int) $cart['converted_order_id'] ?></a>
                          <?php else: ?>
                            <form method="POST" action="/admin/orders" class="adminStatusForm">
                              <input type="hidden" name="tab" value="carts">
                              <input type="hidden" name="record_type" value="cart">
                              <input type="hidden" name="record_id" value="<?= $cartId ?>">
                              <select name="status" aria-label="Trạng thái giỏ hàng #<?= $cartId ?>">
                                <?php foreach (['active', 'abandoned'] as $statusValue): ?>
                                  <option value="<?= $statusValue ?>" <?= $cart['status'] === $statusValue ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cartStatuses[$statusValue], ENT_QUOTES, 'UTF-8') ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                              <button type="submit" class="adminButton adminButtonSmall adminButtonSecondary">Lưu</button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="adminEmptyState" data-catalog-empty hidden>Không tìm thấy dữ liệu phù hợp.</div>
              <div class="catalogPagination" data-catalog-pagination hidden>
                <span data-catalog-page-status></span>
                <button type="button" class="adminButton adminButtonSecondary" data-catalog-show-more>Xem thêm</button>
                <button type="button" class="adminButton adminButtonSecondary" data-catalog-collapse hidden>Thu gọn</button>
              </div>
            <?php endif; ?>
          <?php else: ?>
            <?php if ($orders === []): ?>
              <div class="adminEmptyState">Chưa có đơn hàng phù hợp. Đơn hàng sẽ xuất hiện sau khi khách checkout.</div>
            <?php else: ?>
              <div class="adminTableWrap">
                <table class="adminTable adminCommerceTable">
                  <thead>
                    <tr><th scope="col">Đơn hàng</th><th scope="col">Sản phẩm</th><th scope="col">Tổng tiền</th><th scope="col">Trạng thái</th><th scope="col">Ngày tạo</th><th scope="col"><span class="visuallyHidden">Thao tác</span></th></tr>
                  </thead>
                  <tbody data-catalog-list>
                    <?php foreach ($orders as $order): ?>
                      <?php
                      $orderId = (int) $order['order_id'];
                      $orderItemSummary = implode(' ', array_map(
                          static fn(array $item): string => (string) $item['game_name'],
                          $order['items']
                      ));
                      $searchText = htmlspecialchars(implode(' ', [
                          (string) $orderId,
                          (string) $order['customer_name'],
                          substr((string) $order['session_key'], 0, 8),
                          (string) $order['status'],
                          $orderItemSummary,
                      ]), ENT_QUOTES, 'UTF-8');
                      ?>
                      <tr class="adminProductRow" data-catalog-item data-search="<?= $searchText ?>">
                        <td>
                          <div class="adminRecordIdentity">
                            <strong>#<?= $orderId ?></strong>
                            <small><?= htmlspecialchars((string) $order['customer_name'], ENT_QUOTES, 'UTF-8') ?></small>
                            <small>Phiên <?= htmlspecialchars(substr((string) $order['session_key'], 0, 8), ENT_QUOTES, 'UTF-8') ?></small>
                          </div>
                        </td>
                        <td>
                          <details class="adminRecordDetails">
                            <summary><?= (int) $order['item_count'] ?> sản phẩm</summary>
                            <ul>
                              <?php foreach ($order['items'] as $item): ?>
                                <li>
                                  <?= htmlspecialchars((string) $item['game_name'], ENT_QUOTES, 'UTF-8') ?>
                                  <span>× <?= (int) $item['quantity'] ?> · <?= number_format((int) $item['line_total'], 0, ',', '.') ?> đ</span>
                                </li>
                              <?php endforeach; ?>
                            </ul>
                          </details>
                        </td>
                        <td><strong class="adminPrice"><?= number_format((int) $order['total_price'], 0, ',', '.') ?> đ</strong></td>
                        <td><span class="adminStatusPill is-<?= htmlspecialchars((string) $order['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($orderStatuses[$order['status']] ?? (string) $order['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) $order['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                          <form method="POST" action="/admin/orders" class="adminStatusForm">
                            <input type="hidden" name="tab" value="orders">
                            <input type="hidden" name="record_type" value="order">
                            <input type="hidden" name="record_id" value="<?= $orderId ?>">
                            <select name="status" aria-label="Trạng thái đơn hàng #<?= $orderId ?>">
                              <?php foreach ($orderStatuses as $statusValue => $statusLabel): ?>
                                <option value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>" <?= $order['status'] === $statusValue ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                            <button type="submit" class="adminButton adminButtonSmall adminButtonSecondary">Lưu</button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="adminEmptyState" data-catalog-empty hidden>Không tìm thấy dữ liệu phù hợp.</div>
              <div class="catalogPagination" data-catalog-pagination hidden>
                <span data-catalog-page-status></span>
                <button type="button" class="adminButton adminButtonSecondary" data-catalog-show-more>Xem thêm</button>
                <button type="button" class="adminButton adminButtonSecondary" data-catalog-collapse hidden>Thu gọn</button>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </div>
  </div>
</section>
