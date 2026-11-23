<?php
if (!function_exists('resolveProductImage')) {
    $helperCandidates = [
        __DIR__ . '/../../Helpers/imgrel.php',
        // __DIR__ . '/../../Helpers/ProductImageHelper.php',
        // __DIR__ . '/../../Helpers/ImageHelper.php',
    ];

    foreach ($helperCandidates as $helperFile) {
        if (is_file($helperFile)) {
            require_once $helperFile;
            break;
        }
    }
}
if (!isset($products) || !is_array($products)) {
    $imgDir = __DIR__ . '/../../../public/assets/images/products';
    $scanned = [];

    if (is_dir($imgDir)) {
        $files = scandir($imgDir) ?: [];
        $idx = 1;
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }
            $stem = pathinfo($file, PATHINFO_FILENAME);
            $scanned[] = [
                'id'          => $idx,
                'name'        => ucwords(str_replace(['-', '_'], ' ', $stem)),
                'description' => 'Sản phẩm mẫu tạo tự động từ file ' . $file,
                'price'       => $idx * 100000,
                'image'       => $file,
            ];
            $idx++;
        }
    }

    $products = $scanned;
}
?>
<?php
$allProducts = $products ?? [];
$carouselItems = array_slice($allProducts, 0, 6);
$keywordValue = (string) ($keyword ?? '');

$getProductImg = static function (array $item): string {
    $id = (string) ($item['id'] ?? '');
    $rawImage = (string) ($item['image'] ?? '');

    if (function_exists('resolveProductImage')) {
        return (string) resolveProductImage($rawImage, $id);
    }

    return $rawImage !== ''
        ? 'assets/images/products/' . htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8')
        : '';
};
?>


<section class="storeCatalog" aria-label="Danh sách sản phẩm">
  <header class="storeBanner">
    <div>
      <h1 class="storeBannerTitle">Cửa hàng sản phẩm</h1>
      <p class="storeBannerSub">Khám phá sản phẩm nổi bật hoặc tìm kiếm toàn bộ danh sách bên dưới</p>
    </div>
  </header>

  <!-- 1. Carousel sản phẩm nổi bật ở trên -->
  <?php if (!empty($carouselItems)): ?>
    <section class="featuredCarousel" aria-label="Sản phẩm nổi bật" data-carousel>
      <div class="fcHeader">
        <h2 class="fcHeading">Sản phẩm nổi bật</h2>
        <div class="fcControls">
          <button type="button" class="fcNavBtn" data-carousel-prev aria-label="Sản phẩm trước">&#8249;</button>
          <button type="button" class="fcNavBtn" data-carousel-next aria-label="Sản phẩm tiếp">&#8250;</button>
        </div>
      </div>

      <div class="fcViewport" data-carousel-track>
        <?php foreach ($carouselItems as $item): ?>
          <?php
            $id = (int) ($item['id'] ?? 0);
            $name = htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
            $price = number_format((float) ($item['price'] ?? 0), 0, ',', '.');
            $img = $getProductImg($item);
            echo '<!-- DEBUG CAROUSEL IMG: ' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8') . ' -->';
          ?>
          <article class="fcItem">
            <a href="?page=product&id=<?= $id ?>" class="fcCard">
              <div class="fcThumb">
                <?php if ($img !== ''): ?>
                  <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="fcImg" loading="lazy">
                <?php else: ?>
                  <div class="fcPlaceholder"></div>
                <?php endif; ?>
              </div>
              <div class="fcMeta">
                <strong class="fcName"><?= $name ?></strong>
                <span class="fcPrice"><?= $price ?> đ</span>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- 2. Danh sách sản phẩm dạng list + tìm kiếm từ khóa ở dưới -->
  <section class="catalogSection" aria-label="Tất cả sản phẩm">
    <div class="catalogTop">
      <h2 class="catalogHeading">Tất cả sản phẩm</h2>
      <span class="catalogCount"><?= count($allProducts) ?> sản phẩm</span>
    </div>

    <form method="GET" action="" class="filterForm" role="search">
      <input type="hidden" name="page" value="products">
      <div class="filterBox">
        <input
          type="search"
          name="q"
          value="<?= htmlspecialchars($keywordValue, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Tìm kiếm sản phẩm theo từ khóa..."
          class="filterInput"
          aria-label="Từ khóa sản phẩm"
        >
        <button type="submit" class="filterSubmit">Tìm kiếm</button>
      </div>
      <?php if ($keywordValue !== ''): ?>
        <a href="?page=products" class="filterReset">Xóa lọc</a>
      <?php endif; ?>
    </form>

    <?php if (empty($allProducts)): ?>
      <div class="catalogEmpty">Không tìm thấy sản phẩm nào khớp với từ khóa.</div>
    <?php else: ?>
      <div class="catalogList">
        <?php foreach ($allProducts as $p): ?>
          <?php
            $id = (int) ($p['id'] ?? 0);
            $name = htmlspecialchars((string) ($p['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
            $desc = htmlspecialchars((string) ($p['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $price = number_format((float) ($p['price'] ?? 0), 0, ',', '.');
            $img = $getProductImg($p);
            echo '<!-- DEBUG LIST IMG: ' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8') . ' -->';
          ?>
          <article class="catalogRow">
            <a href="?page=product&id=<?= $id ?>" class="catalogThumbLink" aria-label="<?= $name ?>">
              <div class="catalogThumb">
                <?php if ($img !== ''): ?>
                  <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="fcImg" loading="lazy">
                <?php else: ?>
                  <div class="fcPlaceholder"></div>
                <?php endif; ?>
              </div>
            </a>

            <div class="catalogBody">
              <a href="?page=product&id=<?= $id ?>" class="catalogTitle"><?= $name ?></a>
              <?php if ($desc !== ''): ?>
                <p class="catalogSummary"><?= $desc ?></p>
              <?php endif; ?>
            </div>

            <div class="catalogAside">
              <span class="catalogPrice"><?= $price ?> đ</span>
              <a href="?page=product&id=<?= $id ?>" class="catalogDetailBtn">Chi tiết</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</section>

