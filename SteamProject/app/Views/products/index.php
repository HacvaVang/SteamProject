<?php
require_once __DIR__ . '/../../Helpers/product_data.php';

$styles = ['/public/assets/css/product.css'];
$scripts = [
    '/public/assets/js/product.js',
    '/public/assets/js/catalog-search.js',
];

$isLoggedIn = !empty($currentUser ?? ($_SESSION['user'] ?? null));
$rawQuery = trim((string) ($_GET['q'] ?? ''));
$searchQuery = htmlspecialchars($rawQuery, ENT_QUOTES, 'UTF-8');

$products = getStoreProducts(is_array($products ?? null) ? $products : []);

if ($rawQuery !== '') {
    $needle = mb_strtolower($rawQuery, 'UTF-8');
    $products = array_values(array_filter($products, static function (array $item) use ($needle): bool {
        $haystack = mb_strtolower(
            (string) ($item['name'] ?? '') . ' ' . (string) ($item['summary'] ?? ''),
            'UTF-8'
        );
        return mb_strpos($haystack, $needle) !== false;
    }));
}

$featuredProducts = array_slice($products, 0, 6);
?>
<section
  class="storeCatalog"
  aria-label="Danh sách sản phẩm"
  data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
  data-login-url="/login"
  data-cart-url="/cart"
>
  <header class="storeBanner">
    <div>
      <h1 class="storeBannerTitle">Cửa hàng sản phẩm</h1>
      <p class="storeBannerSub">Khám phá sản phẩm nổi bật hoặc tìm kiếm toàn bộ danh sách bên dưới</p>
    </div>
  </header>

  <section class="featuredCarousel" aria-label="Sản phẩm nổi bật" data-carousel>
    <div class="fcHeader">
      <h2 class="fcHeading">Sản phẩm nổi bật</h2>
      <div class="fcControls">
        <button type="button" class="fcNavBtn" data-carousel-prev aria-label="Sản phẩm trước">&#8249;</button>
        <button type="button" class="fcNavBtn" data-carousel-next aria-label="Sản phẩm tiếp">&#8250;</button>
      </div>
    </div>

    <div class="fcViewport" data-carousel-track>
      <?php foreach ($featuredProducts as $item): ?>
        <?php
        $id = htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
        $basePriceNum = (int) ($item['base_price'] ?? 0);
        $priceNum = (int) ($item['final_price'] ?? $item['price'] ?? 0);
        $priceText = number_format($priceNum, 0, ',', '.') . ' đ';
        $basePriceText = number_format($basePriceNum, 0, ',', '.') . ' đ';
        $discount = (float) ($item['discount_check'] ?? 0);
        $rating = $item['rating'];
        $ratingText = $rating === null
            ? 'Chưa có đánh giá'
            : number_format((float) $rating, 1, ',', '.') . ' / 5 (' . (int) $item['review_count'] . ')';
        $imgSrc = resolveProductImage((string) ($item['image'] ?? ''), (string) ($item['id'] ?? ''));
        ?>
        <article class="fcItem">
          <a href="/product?id=<?= $id ?>" class="fcCard">
            <div class="fcThumb">
              <?php if ($imgSrc !== ''): ?>
                <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="fcImg">
              <?php else: ?>
                <div class="fcPlaceholder"></div>
              <?php endif; ?>
            </div>
            <div class="fcMeta">
              <strong class="fcName"><?= $name ?></strong>
              <span class="fcPrice">
                <?php if ($discount > 0): ?><del><?= $basePriceText ?></del><?php endif; ?>
                <strong><?= $priceText ?></strong>
                <?php if ($discount > 0): ?><span class="catalogDiscount">-<?= rtrim(rtrim(number_format($discount, 2, ',', '.'), '0'), ',') ?>%</span><?php endif; ?>
              </span>
              <span class="productRating"><?= htmlspecialchars($ratingText, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="catalogSection" aria-label="Tất cả sản phẩm">
    <div class="catalogTop">
      <h2 class="catalogHeading">Tất cả sản phẩm</h2>
      <span class="catalogCount"><?= count($products) ?> sản phẩm</span>
    </div>

    <form method="GET" action="/products" class="filterForm" role="search">
      <div class="filterBox">
        <input
          type="search"
          name="q"
          value="<?= $searchQuery ?>"
          placeholder="Tìm kiếm sản phẩm theo từ khóa..."
          class="filterInput"
          data-catalog-search
          data-server-filtered="<?= $rawQuery !== '' ? '1' : '0' ?>"
          aria-label="Từ khóa sản phẩm"
        >
        <button type="submit" class="filterSubmit">Tìm kiếm</button>
      </div>
    </form>

    <div class="emptyRow" data-catalog-empty <?= empty($products) ? '' : 'hidden' ?>>
      Không tìm thấy sản phẩm nào.
    </div>
    <div class="catalogList" data-catalog-list>
      <?php foreach ($products as $item): ?>
        <?php
        $id = htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
        $summary = htmlspecialchars((string) ($item['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tags = is_array($item['tags'] ?? null) ? $item['tags'] : [];
        $searchText = htmlspecialchars(
            trim((string) ($item['id'] ?? '') . ' ' . (string) ($item['name'] ?? '') . ' ' . (string) ($item['summary'] ?? '') . ' ' . implode(' ', $tags)),
            ENT_QUOTES,
            'UTF-8'
        );
        $basePriceNum = (int) ($item['base_price'] ?? 0);
        $priceNum = (int) ($item['final_price'] ?? $item['price'] ?? 0);
        $priceText = number_format($priceNum, 0, ',', '.') . ' đ';
        $basePriceText = number_format($basePriceNum, 0, ',', '.') . ' đ';
        $discount = (float) ($item['discount_check'] ?? 0);
        $rating = $item['rating'];
        $ratingText = $rating === null
            ? 'Chưa có đánh giá'
            : number_format((float) $rating, 1, ',', '.') . ' / 5 (' . (int) $item['review_count'] . ')';
        $imgSrc = resolveProductImage((string) ($item['image'] ?? ''), (string) ($item['id'] ?? ''));
        ?>
        <article class="catalogRow" data-catalog-item data-search="<?= $searchText ?>">
          <a href="/product?id=<?= $id ?>" class="catalogThumbLink" aria-label="<?= $name ?>">
            <div class="catalogThumb">
              <?php if ($imgSrc !== ''): ?>
                <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="catalogImg">
              <?php else: ?>
                <div class="fcPlaceholder"></div>
              <?php endif; ?>
            </div>
          </a>

          <div class="catalogBody">
            <a href="/product?id=<?= $id ?>" class="catalogTitle"><?= $name ?></a>
            <?php if ($summary !== ''): ?>
              <p class="catalogSummary"><?= $summary ?></p>
            <?php endif; ?>
            <p class="productRating"><?= htmlspecialchars($ratingText, ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($tags !== []): ?>
              <div class="catalogTags" aria-label="Thẻ sản phẩm">
                <?php foreach ($tags as $tag): ?>
                  <span class="productTag"><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="catalogAside">
            <span class="catalogPrice"><?= $priceText ?></span>
            <?php if ($discount > 0): ?>
              <span class="catalogOriginalPrice"><?= $basePriceText ?></span>
              <span class="catalogDiscount">-<?= rtrim(rtrim(number_format($discount, 2, ',', '.'), '0'), ',') ?>%</span>
            <?php endif; ?>
            <div class="catalogActions">
              <a href="/product?id=<?= $id ?>" class="catalogDetailBtn">Chi tiết</a>
              <button
                type="button"
                class="catalogAddCartBtn"
                data-add-to-cart
                data-product-id="<?= $id ?>"
                data-product-name="<?= $name ?>"
              >
                Thêm vào giỏ
              </button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</section>
