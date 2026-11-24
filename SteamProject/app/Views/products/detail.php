<?php
require_once __DIR__ . '/../../Helpers/product_data.php';

$isLoggedIn = !empty($currentUser ?? ($_SESSION['user'] ?? null));
$allProducts = getStoreProducts(is_array($products ?? null) ? $products : []);
$reqId = trim((string) ($_GET['id'] ?? ''));
$product = is_array($product ?? null) ? $product : findStoreProductById($allProducts, $reqId);
?>
<section
  class="productDetailWrap"
  aria-label="Chi tiết sản phẩm"
  data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
  data-login-url="?page=login"
>
  <div class="detailTopBar">
    <a href="?page=products" class="detailBackLink">&#8592; Quay lại cửa hàng</a>
    <div class="detailTopActions">
      <?php if ($product): ?>
        <?php $topId = htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
        <a href="?page=review&id=<?= $topId ?>" class="detailReviewLink">Đánh giá sản phẩm</a>
      <?php endif; ?>
      <a href="?page=cart" class="detailCartLink">Xem giỏ hàng</a>
    </div>
  </div>

  <?php if (!$product): ?>
    <div class="emptyRow">
      <p>Không tìm thấy sản phẩm bạn yêu cầu.</p>
      <a href="?page=products" class="catalogDetailBtn">Về danh sách sản phẩm</a>
    </div>
  <?php else: ?>
    <?php
    $id = htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars((string) ($product['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
    $summary = htmlspecialchars((string) ($product['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars((string) ($product['description'] ?? ($product['summary'] ?? '')), ENT_QUOTES, 'UTF-8');
    $priceNum = (int) ($product['price'] ?? 0);
    $priceText = number_format($priceNum, 0, ',', '.') . ' đ';
    $imgSrc = resolveProductImage((string) ($product['image'] ?? ''), (string) ($product['id'] ?? ''));
    ?>
    <article class="productDetailCard">
      <div class="productDetailMedia">
        <?php if ($imgSrc !== ''): ?>
          <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="productDetailImg">
        <?php else: ?>
          <div class="fcPlaceholder"></div>
        <?php endif; ?>
      </div>

      <div class="productDetailInfo">
        <h1 class="productDetailTitle"><?= $name ?></h1>
        <div class="productDetailPrice"><?= $priceText ?></div>

        <?php if ($summary !== ''): ?>
          <p class="productDetailSummary"><?= $summary ?></p>
        <?php endif; ?>

        <?php if ($description !== ''): ?>
          <div class="productDetailDesc">
            <h2>Mô tả</h2>
            <p><?= nl2br($description) ?></p>
          </div>
        <?php endif; ?>

        <div class="productDetailActions">
          <button
            type="button"
            class="catalogAddCartBtn"
            data-add-to-cart
            data-product-id="<?= $id ?>"
            data-product-name="<?= $name ?>"
          >
            Thêm vào giỏ hàng
          </button>
          <a href="?page=review&id=<?= $id ?>" class="detailReviewBtn">Đánh giá sản phẩm</a>
          <a href="?page=cart" class="catalogDetailBtn">Đi tới giỏ hàng</a>
        </div>
      </div>
    </article>
  <?php endif; ?>
</section>