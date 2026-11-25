<?php
require_once __DIR__ . '/../../Helpers/product_data.php';

$isLoggedIn = !empty($currentUser ?? ($_SESSION['user'] ?? null));
$allProducts = getStoreProducts(is_array($products ?? null) ? $products : []);

$productCatalogMap = [];
foreach ($allProducts as $p) {
    $pid = (string) ($p['id'] ?? '');
    if ($pid === '') {
        continue;
    }
    $productCatalogMap[$pid] = [
        'id'    => $pid,
        'name'  => (string) ($p['name'] ?? 'Sản phẩm'),
        'price' => (int) ($p['price'] ?? 0),
        'image' => resolveProductImage((string) ($p['image'] ?? ''), $pid),
    ];
}
?>
<section
  class="cartPageWrap"
  aria-label="Giỏ hàng"
  data-cart-page
  data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
>
  <header class="storeBanner">
    <div>
      <h1 class="storeBannerTitle">Giỏ hàng của bạn</h1>
      <p class="storeBannerSub">Kiểm tra sản phẩm đã chọn trước khi đặt mua</p>
    </div>
    <a href="/products" class="catalogDetailBtn">Tiếp tục mua sắm</a>
  </header>

  <?php if (!$isLoggedIn): ?>
    <div class="cartNoticeBox">
      <p>Bạn cần đăng nhập để đồng bộ và quản lý giỏ hàng đầy đủ.</p>
      <a href="/login" class="catalogAddCartBtn">Đăng nhập ngay</a>
    </div>
  <?php endif; ?>

  <div class="cartContainer" data-cart-container>
    <div class="emptyRow" data-cart-empty>Giỏ hàng đang trống.</div>
    <div class="cartList" data-cart-items></div>
  </div>

  <footer class="cartFooter" data-cart-footer hidden>
    <div class="cartSummary">
      <span>Tổng cộng:</span>
      <strong class="cartTotalPrice" data-cart-total>0 đ</strong>
    </div>
    <div class="cartFooterActions">
      <button type="button" class="cartClearBtn" data-cart-clear>Xóa giỏ hàng</button>
      <button type="button" class="catalogAddCartBtn" data-cart-checkout>Thanh toán</button>
    </div>
    <p class="cartCheckoutMsg" data-cart-message role="status"></p>
  </footer>
</section>

<script>
  window.__CATALOG_MAP__ = <?= json_encode($productCatalogMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>