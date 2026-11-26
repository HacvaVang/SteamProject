<?php
require_once __DIR__ . '/../../Helpers/product_data.php';
require_once __DIR__ . '/../../Helpers/store_commerce.php';

$styles = [
    '/public/assets/css/product.css',
    '/public/assets/css/cart.css',
];
$scripts = ['/public/assets/js/cart.js'];

$isLoggedIn = !empty($currentUser ?? ($_SESSION['user'] ?? null));
$allProducts = getStoreProducts(is_array($products ?? null) ? $products : []);
$storeCart = getStoreCartForSession(session_id());

$productCatalogMap = [];
foreach ($allProducts as $p) {
    $pid = (string) ($p['id'] ?? '');
    if ($pid === '') {
        continue;
    }
    $productCatalogMap[$pid] = [
        'id'    => $pid,
        'name'  => (string) ($p['name'] ?? 'Sản phẩm'),
        'price' => (int) ($p['final_price'] ?? $p['price'] ?? 0),
        'image' => resolveProductImage((string) ($p['image'] ?? ''), $pid),
    ];
}
?>
<section
  class="cartPageWrap"
  aria-label="Giỏ hàng"
  data-cart-page
  data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
  data-cart-sync-url="/cart/sync"
  data-cart-checkout-url="/cart/checkout"
>
  <header class="cartPageHeader">
    <div>
      <p class="cartBreadcrumb">Store <span>/</span> Giỏ hàng</p>
      <h1>Giỏ hàng của bạn</h1>
      <p>Kiểm tra sản phẩm đã chọn trước khi đặt mua.</p>
    </div>
    <a href="/products" class="cartContinueLink">Tiếp tục mua sắm</a>
  </header>

  <?php if (!$isLoggedIn): ?>
    <div class="cartNoticeBox" role="status">
      <p>Bạn cần đăng nhập để đồng bộ và quản lý giỏ hàng đầy đủ.</p>
      <a href="/login" class="cartPrimaryButton">Đăng nhập ngay</a>
    </div>
  <?php endif; ?>

  <div class="cartLayout">
    <section class="cartContainer" data-cart-container aria-label="Sản phẩm trong giỏ">
      <div class="cartSectionHeading">
        <div><h2>Sản phẩm đã chọn</h2><p>Giỏ hàng được lưu theo phiên làm việc.</p></div>
        <span class="cartSecureBadge">Giỏ hàng</span>
      </div>
      <div class="emptyRow cartEmptyState" data-cart-empty>Giỏ hàng đang trống.</div>
      <div class="cartList" data-cart-items></div>
    </section>
    <aside class="cartSummaryPanel" aria-label="Tóm tắt đơn hàng">
      <h2>Tóm tắt đơn hàng</h2>
      <div class="cartSummaryLine"><span>Tạm tính</span><strong data-cart-total>0 đ</strong></div>
      <div class="cartSummaryLine"><span>Phí thanh toán</span><strong>Miễn phí</strong></div>
      <div class="cartGrandTotal"><span>Tổng cộng</span><strong data-cart-total>0 đ</strong></div>
      <div class="cartFooterActions">
        <button type="button" class="cartPrimaryButton" data-cart-checkout>Thanh toán</button>
        <button type="button" class="cartClearBtn" data-cart-clear>Xóa toàn bộ giỏ hàng</button>
      </div>
      <p class="cartCheckoutMsg" data-cart-message role="status" aria-live="polite"></p>
      <p class="cartCheckoutNote">Đơn hàng sẽ được lưu và hiển thị trong trang quản trị sau khi checkout.</p>
    </aside>
  </div>
  <p class="cartSyncMessage" data-cart-sync-message role="status" aria-live="polite"></p>
</section>

<script>
  window.__CATALOG_MAP__ = <?= json_encode($productCatalogMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
  window.__STORE_CART__ = <?= json_encode($storeCart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
</script>