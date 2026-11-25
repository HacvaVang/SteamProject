<?php
require_once __DIR__ . '/../../Helpers/product_data.php';

$styles = ['/public/assets/css/product.css'];
$scripts = ['/public/assets/js/product.js'];

$isLoggedIn = !empty($currentUser ?? ($_SESSION['user'] ?? null));
$allProducts = getStoreProducts(is_array($products ?? null) ? $products : []);
$reqId = trim((string) ($_GET['id'] ?? ''));
$product = is_array($product ?? null) ? $product : findStoreProductById($allProducts, $reqId);
?>
<section
  class="productDetailWrap"
  aria-label="Chi tiết sản phẩm"
  data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
  data-login-url="/login"
>
  <div class="detailTopBar">
    <a href="/products" class="detailBackLink">&#8592; Quay lại cửa hàng</a>
    <div class="detailTopActions">
      <?php if ($product): ?>
        <?php $topId = htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
        <a href="/review?id=<?= $topId ?>" class="detailReviewLink">Đánh giá sản phẩm</a>
      <?php endif; ?>
      <a href="/cart" class="detailCartLink">Xem giỏ hàng</a>
    </div>
  </div>

  <?php if (!$product): ?>
    <div class="emptyRow">
      <p>Không tìm thấy sản phẩm bạn yêu cầu.</p>
      <a href="/products" class="catalogDetailBtn">Về danh sách sản phẩm</a>
    </div>
  <?php else: ?>
    <?php
    $id = htmlspecialchars((string) ($product['id'] ?? ''), ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars((string) ($product['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
    $summary = htmlspecialchars((string) ($product['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars((string) ($product['description'] ?? ($product['summary'] ?? '')), ENT_QUOTES, 'UTF-8');
    $basePriceNum = (int) ($product['base_price'] ?? 0);
    $priceNum = (int) ($product['final_price'] ?? $product['price'] ?? 0);
    $priceText = number_format($priceNum, 0, ',', '.') . ' đ';
    $basePriceText = number_format($basePriceNum, 0, ',', '.') . ' đ';
    $discount = (float) ($product['discount_check'] ?? 0);
    $rating = $product['rating'];
    $ratingText = $rating === null
        ? 'Chưa có đánh giá'
        : number_format((float) $rating, 1, ',', '.') . ' / 5 (' . (int) $product['review_count'] . ' đánh giá)';
    $tags = is_array($product['tags'] ?? null) ? $product['tags'] : [];
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
        <p class="productRating"><?= htmlspecialchars($ratingText, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="productDetailPrice">
          <?php if ($discount > 0): ?><del class="catalogOriginalPrice"><?= $basePriceText ?></del><?php endif; ?>
          <strong><?= $priceText ?></strong>
          <?php if ($discount > 0): ?><span class="catalogDiscount">-<?= rtrim(rtrim(number_format($discount, 2, ',', '.'), '0'), ',') ?>%</span><?php endif; ?>
        </div>

        <?php if ($tags !== []): ?>
          <div class="catalogTags" aria-label="Thẻ sản phẩm">
            <?php foreach ($tags as $tag): ?>
              <span class="productTag"><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

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
          <a href="/review?id=<?= $id ?>" class="detailReviewBtn">Đánh giá sản phẩm</a>
          <a href="/cart" class="catalogDetailBtn">Đi tới giỏ hàng</a>
        </div>
      </div>
    </article>
  <?php endif; ?>
</section>