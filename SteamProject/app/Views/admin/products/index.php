<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';

$styles = [
    '/public/assets/css/product.css',
    '/public/assets/css/admin-products.css',
];
$scripts = ['/public/assets/js/catalog-search.js'];
$adminNotice = '';
$adminError = '';
$products = getStoreProducts(is_array($products ?? null) ? $products : []);

$sessionUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin = isAdminUser($sessionUser);

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['switch_mode'])) {
    $action = (string) ($_POST['admin_action'] ?? '');

    if ($action === 'save') {
        $submittedId = trim((string) ($_POST['id'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $basePrice = max(0, (int) ($_POST['base_price'] ?? 0));
        $discountRaw = trim((string) ($_POST['discount_check'] ?? '0'));
        $discount = is_numeric($discountRaw) ? (float) $discountRaw : null;
        $image = trim((string) ($_POST['image'] ?? ''));
        $summary = trim((string) ($_POST['summary'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $tags = array_values(array_unique(array_filter(
            array_map('trim', explode(',', (string) ($_POST['tags'] ?? ''))),
            static fn(string $tag): bool => $tag !== ''
        )));

        if ($name === '') {
            $adminError = 'Vui lòng nhập tên sản phẩm.';
        } elseif ($discount === null || $discount < 0 || $discount > 100) {
            $adminError = 'Phần trăm giảm giá phải nằm trong khoảng 0–100.';
        } else {
            $existingProduct = $submittedId !== '' ? findStoreProductById($products, $submittedId) : null;
            $isEditing = $existingProduct !== null;
            $targetId = $isEditing ? $submittedId : generateNextProductId($products);

            $record = [
                'game_id'        => $targetId,
                'name'           => $name,
                'summary'        => $summary,
                'description'    => $description !== '' ? $description : $summary,
                'base_price'     => $basePrice,
                'discount_check' => $discount,
                'tags'           => $tags,
                'reviews'        => $existingProduct['reviews'] ?? [],
                'image'          => $image,
            ];

            if ($isEditing) {
                foreach ($products as $idx => $existing) {
                    if ((string) ($existing['game_id'] ?? $existing['id'] ?? '') === $targetId) {
                        $products[$idx] = $record;
                        break;
                    }
                }
                $adminNotice = 'Đã cập nhật sản phẩm #' . $targetId . '.';
            } else {
                $products[] = $record;
                $adminNotice = 'Đã thêm sản phẩm mới #' . $targetId . '.';
            }

            if (!saveStoreProducts($products)) {
                $adminError = 'Không thể lưu danh mục sản phẩm vào SQLite.';
            }
        }
    } elseif ($action === 'delete') {
        $deleteId = trim((string) ($_POST['id'] ?? ''));
        if ($deleteId !== '') {
            $beforeCount = count($products);
            $products = array_values(array_filter(
                $products,
                static fn(array $item): bool => (string) ($item['id'] ?? '') !== $deleteId
            ));

            if (count($products) < $beforeCount) {
                if (saveStoreProducts($products)) {
                    $adminNotice = 'Đã xóa sản phẩm #' . $deleteId . '.';
                } else {
                    $adminError = 'Không thể cập nhật danh mục SQLite sau khi xóa.';
                }
            }
        }
    } elseif ($action === 'sync_images') {
        $updatedImages = syncStoreProductImages();
        $products = getStoreProducts();
        $adminNotice = 'Đã đồng bộ ảnh cho ' . $updatedImages . ' trò chơi.';
    }
}

$products = getStoreProducts($products);

$adminSearch = trim((string) ($_GET['q'] ?? ''));
$adminSearchEscaped = htmlspecialchars($adminSearch, ENT_QUOTES, 'UTF-8');
$adminSearchMatches = $products;
if ($adminSearch !== '') {
    $searchTokens = preg_split('/\s+/', normalizeCatalogSearchText($adminSearch), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $adminSearchMatches = array_values(array_filter($products, static function (array $item) use ($searchTokens): bool {
        $tags = is_array($item['tags'] ?? null) ? $item['tags'] : [];
        $haystack = normalizeCatalogSearchText(implode(' ', [
            (string) ($item['game_id'] ?? $item['id'] ?? ''),
            (string) ($item['name'] ?? ''),
            (string) ($item['summary'] ?? ''),
            (string) ($item['description'] ?? ''),
            implode(' ', $tags),
            (string) ($item['base_price'] ?? ''),
            number_format((float) ($item['base_price'] ?? 0), 0, '.', '.'),
            (string) ($item['final_price'] ?? ''),
            number_format((float) ($item['final_price'] ?? 0), 0, '.', '.'),
            (string) ($item['discount_check'] ?? ''),
            (string) ($item['rating'] ?? ''),
        ]));
        foreach ($searchTokens as $token) {
            if (strpos($haystack, $token) === false) {
                return false;
            }
        }
        return true;
    }));
}
$adminMatchedIds = array_fill_keys(array_map(
    static fn(array $item): string => (string) ($item['game_id'] ?? $item['id'] ?? ''),
    $adminSearchMatches
), true);

$editId = trim((string) ($_GET['edit'] ?? ''));
$editingProduct = ($isAdmin && $editId !== '') ? findStoreProductById($products, $editId) : null;

$formId = htmlspecialchars((string) ($editingProduct['id'] ?? ''), ENT_QUOTES, 'UTF-8');
$formName = htmlspecialchars((string) ($editingProduct['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$formBasePrice = (int) ($editingProduct['base_price'] ?? 0);
$formDiscount = number_format((float) ($editingProduct['discount_check'] ?? 0), 2, '.', '');
$formTags = htmlspecialchars(implode(', ', $editingProduct['tags'] ?? []), ENT_QUOTES, 'UTF-8');
$formImage = htmlspecialchars((string) ($editingProduct['image'] ?? ''), ENT_QUOTES, 'UTF-8');
$formSummary = htmlspecialchars((string) ($editingProduct['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
$formDesc = htmlspecialchars((string) ($editingProduct['description'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<section class="adminProductsWrap" aria-label="Quản lý sản phẩm">
  <header class="storeBanner">
    <div>
      <h1 class="storeBannerTitle">Quản lý sản phẩm (Admin)</h1>
      <p class="storeBannerSub">Thêm, chỉnh sửa hoặc xóa sản phẩm trong cửa hàng</p>
    </div>
    <div class="adminFormActions">
      <?php if ($isAdmin): ?>
        <form method="POST" action="/admin/products" class="inlineDeleteForm">
          <input type="hidden" name="admin_action" value="sync_images">
          <button type="submit" class="catalogDetailBtn">Đồng bộ ảnh game</button>
        </form>
      <?php endif; ?>
      <a href="/products" class="catalogDetailBtn">Xem trang Store</a>
    </div>
  </header>

  <?php if (!$isAdmin): ?>
    <section class="adminLockedBox" aria-label="Yêu cầu quyền Admin">
      <h2 class="adminFormHeading">Truy cập bị hạn chế</h2>
      <p class="storeBannerSub">
        Trang <strong>Quản lý sản phẩm</strong> chỉ dành cho tài khoản có quyền <strong>admin</strong>. Bạn có thể bấm nút chuyển quyền ở thanh phía dưới để kiểm thử nhanh.
      </p>
      <div class="adminLockedActions">
        <a href="/login" class="catalogDetailBtn">Đăng nhập tài khoản Admin</a>
        <a href="/products" class="catalogAddCartBtn">Quay lại Store</a>
      </div>
    </section>
  <?php else: ?>
    <?php if ($adminNotice !== ''): ?>
      <div class="adminAlertSuccess" role="status"><?= htmlspecialchars($adminNotice, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($adminError !== ''): ?>
      <div class="adminAlertError" role="alert"><?= htmlspecialchars($adminError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/products" class="adminProductForm">
      <input type="hidden" name="admin_action" value="save">
      <input type="hidden" name="id" value="<?= $formId ?>">

      <h2 class="adminFormHeading">
        <?= $editingProduct ? 'Chỉnh sửa sản phẩm #' . $formId : 'Thêm sản phẩm mới' ?>
      </h2>

      <div class="adminFormGrid">
        <div class="adminField">
          <label for="prodName">Tên sản phẩm</label>
          <input id="prodName" type="text" name="name" value="<?= $formName ?>" required>
        </div>

        <div class="adminField">
          <label for="prodBasePrice">Giá gốc (VNĐ)</label>
          <input id="prodBasePrice" type="number" name="base_price" min="0" step="1000" value="<?= $formBasePrice ?>" required>
        </div>

        <div class="adminField">
          <label for="prodDiscount">Giảm giá (%)</label>
          <input id="prodDiscount" type="number" name="discount_check" min="0" max="100" step="0.01" value="<?= $formDiscount ?>" required>
        </div>

        <div class="adminField">
          <label for="prodImage">Tên file ảnh (trong assets/images/products)</label>
          <input id="prodImage" type="text" name="image" value="<?= $formImage ?>" placeholder="vd: league.jpeg">
        </div>

        <div class="adminField">
          <label for="prodTags">Tag (phân cách bằng dấu phẩy)</label>
          <input id="prodTags" type="text" name="tags" value="<?= $formTags ?>" placeholder="action, multiplayer">
        </div>

        <div class="adminField">
          <label for="prodSummary">Tóm tắt ngắn</label>
          <input id="prodSummary" type="text" name="summary" value="<?= $formSummary ?>">
        </div>
      </div>

      <div class="adminField">
        <label for="prodDesc">Mô tả chi tiết</label>
        <textarea id="prodDesc" name="description" rows="3"><?= $formDesc ?></textarea>
      </div>

      <div class="adminFormActions">
        <button type="submit" class="catalogAddCartBtn">
          <?= $editingProduct ? 'Lưu thay đổi' : 'Thêm sản phẩm' ?>
        </button>
        <?php if ($editingProduct): ?>
          <a href="/admin/products" class="catalogDetailBtn">Hủy sửa</a>
        <?php endif; ?>
      </div>
    </form>

    <section class="catalogSection" aria-label="Danh sách sản phẩm quản trị" data-page-size="12">
      <div class="catalogTop">
        <h2 class="catalogHeading">Danh sách sản phẩm</h2>
        <span class="catalogCount" data-catalog-count><?= count($adminSearchMatches) ?> sản phẩm</span>
      </div>

      <form method="GET" action="/admin/products" class="filterForm" role="search">
        <div class="filterBox">
          <input
            type="search"
            name="q"
            value="<?= $adminSearchEscaped ?>"
            class="filterInput"
            data-catalog-search
            placeholder="Tìm mã, tên, mô tả, tag, giá hoặc điểm đánh giá..."
            aria-label="Tìm kiếm sản phẩm quản trị"
          >
          <button type="submit" class="filterSubmit">Tìm kiếm</button>
        </div>
      </form>

      <div class="emptyRow" data-catalog-empty <?= count($adminSearchMatches) === 0 ? '' : 'hidden' ?>>
        <?= empty($products) ? 'Chưa có sản phẩm nào.' : 'Không tìm thấy sản phẩm nào.' ?>
      </div>
      <div class="catalogList" data-catalog-list>
        <?php foreach ($products as $item): ?>
          <?php
          $rawId = (string) ($item['game_id'] ?? $item['id'] ?? '');
          $id = htmlspecialchars($rawId, ENT_QUOTES, 'UTF-8');
          $name = htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
          $summary = htmlspecialchars((string) ($item['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
          $tags = is_array($item['tags'] ?? null) ? $item['tags'] : [];
          $searchText = htmlspecialchars(
              trim(implode(' ', [
                  $rawId,
                  (string) ($item['name'] ?? ''),
                  (string) ($item['summary'] ?? ''),
                  (string) ($item['description'] ?? ''),
                  implode(' ', $tags),
                  (string) ($item['base_price'] ?? ''),
                  number_format((float) ($item['base_price'] ?? 0), 0, '.', '.'),
                  (string) ($item['final_price'] ?? ''),
                  number_format((float) ($item['final_price'] ?? 0), 0, '.', '.'),
                  (string) ($item['discount_check'] ?? ''),
                  (string) ($item['rating'] ?? ''),
              ])),
              ENT_QUOTES,
              'UTF-8'
          );
          $basePriceText = number_format((int) ($item['base_price'] ?? 0), 0, ',', '.') . ' đ';
          $priceText = number_format((int) ($item['final_price'] ?? $item['price'] ?? 0), 0, ',', '.') . ' đ';
          $discount = (float) ($item['discount_check'] ?? 0);
          $rating = $item['rating'];
          $ratingText = $rating === null
              ? 'Chưa có đánh giá'
              : number_format((float) $rating, 1, ',', '.') . ' / 5 (' . (int) $item['review_count'] . ')';
          $imgSrc = resolveProductImage((string) ($item['image'] ?? ''), (string) ($item['id'] ?? ''));
          ?>
          <article class="catalogRow" data-catalog-item data-search="<?= $searchText ?>" <?= isset($adminMatchedIds[$rawId]) ? '' : 'hidden' ?>>
            <div class="catalogThumb">
              <?php if ($imgSrc !== ''): ?>
                <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $name ?>" class="catalogImg">
              <?php else: ?>
                <div class="fcPlaceholder"></div>
              <?php endif; ?>
            </div>

            <div class="catalogBody">
              <strong class="catalogTitle">#<?= $id ?> — <?= $name ?></strong>
              <?php if ($summary !== ''): ?>
                <p class="catalogSummary"><?= $summary ?></p>
              <?php endif; ?>
              <p class="productRating"><?= htmlspecialchars($ratingText, ENT_QUOTES, 'UTF-8') ?></p>
              <?php if ($tags !== []): ?>
                <div class="catalogTags">
                  <?php foreach ($tags as $tag): ?>
                    <span class="productTag"><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="catalogAside">
              <span class="catalogPrice"><?= $priceText ?></span>
              <span>Giá gốc: <?= $basePriceText ?></span>
              <?php if ($discount > 0): ?>
                <span class="catalogDiscount">Giảm <?= rtrim(rtrim(number_format($discount, 2, ',', '.'), '0'), ',') ?>%</span>
              <?php endif; ?>
              <div class="adminRowActions">
                <a href="/admin/products?edit=<?= $id ?>" class="catalogDetailBtn">Sửa</a>
                <form method="POST" action="/admin/products" class="inlineDeleteForm" onsubmit="return confirm('Xóa sản phẩm này?');">
                  <input type="hidden" name="admin_action" value="delete">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit" class="adminDeleteBtn">Xóa</button>
                </form>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="catalogPagination" data-catalog-pagination hidden>
        <span data-catalog-page-status></span>
        <button type="button" class="filterSubmit" data-catalog-show-more>Xem thêm</button>
        <button type="button" class="filterReset" data-catalog-collapse hidden>Thu gọn</button>
      </div>
    </section>
  <?php endif; ?>
</section>