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
$productTotal = count($products);
$discountedProductCount = count(array_filter(
    $products,
    static fn(array $item): bool => (float) ($item['discount_check'] ?? 0) > 0
));
$reviewedProductCount = count(array_filter(
    $products,
    static fn(array $item): bool => ($item['rating'] ?? null) !== null
));

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
  <aside class="adminSidebar" aria-label="Điều hướng quản trị">
    <a class="adminBrand" href="/admin/products">
      <span class="adminBrandMark" aria-hidden="true">S</span>
      <span class="adminBrandText"><strong>Steam</strong><small>ADMIN PANEL</small></span>
    </a>
    <p class="adminNavLabel">MENU</p>
    <nav class="adminSidebarNav">
      <a class="adminNavLink is-active" href="/admin/products" aria-current="page">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4zM14 4h6v4h-6zM14 11h6v9h-6zM4 14h7v6H4z"/></svg>
        <span>Sản phẩm</span>
      </a>
      <a class="adminNavLink" href="/admin/qa">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v11H9l-5 4zM8 9h8M8 12h5"/></svg>
        <span>Q&amp;A</span>
      </a>
      <a class="adminNavLink" href="/admin/orders">
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
        <p class="adminBreadcrumb">Admin <span>/</span> Catalog</p>
        <h1>Quản lý sản phẩm</h1>
      </div>
      <div class="adminTopbarActions">
        <?php if ($isAdmin): ?>
          <form method="POST" action="/admin/products" class="inlineDeleteForm">
            <input type="hidden" name="admin_action" value="sync_images">
            <button type="submit" class="adminButton adminButtonSecondary">Đồng bộ ảnh</button>
          </form>
        <?php endif; ?>
        <a href="/products" class="adminButton adminButtonPrimary">Mở Store</a>
      </div>
    </header>

    <div class="adminDashboardContent">

  <?php if (!$isAdmin): ?>
    <section class="adminLockedBox" aria-label="Yêu cầu quyền Admin">
      <span class="adminLockIcon" aria-hidden="true">!</span>
      <div>
        <h2>Truy cập bị hạn chế</h2>
        <p>Trang quản lý sản phẩm chỉ dành cho tài khoản có quyền Admin.</p>
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

    <section class="adminStats" aria-label="Thống kê danh mục">
      <article class="adminStatCard">
        <span class="adminStatIcon adminStatIconBlue" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM8 9h8M8 13h5"/></svg>
        </span>
        <div><span>Tổng sản phẩm</span><strong><?= $productTotal ?></strong><small>Game trong danh mục</small></div>
      </article>
      <article class="adminStatCard">
        <span class="adminStatIcon adminStatIconGreen" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="m12 3 2.2 5.2 5.6.5-4.3 3.6 1.3 5.5-4.8-2.9-4.8 2.9 1.3-5.5-4.3-3.6 5.6-.5z"/></svg>
        </span>
        <div><span>Đang giảm giá</span><strong><?= $discountedProductCount ?></strong><small>Sản phẩm có ưu đãi</small></div>
      </article>
      <article class="adminStatCard">
        <span class="adminStatIcon adminStatIconOrange" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/></svg>
        </span>
        <div><span>Đã có đánh giá</span><strong><?= $reviewedProductCount ?></strong><small>Game có rating</small></div>
      </article>
      <article class="adminStatCard">
        <span class="adminStatIcon adminStatIconPurple" aria-hidden="true">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4M8 11h6"/></svg>
        </span>
        <div><span>Kết quả tìm kiếm</span><strong data-catalog-count><?= count($adminSearchMatches) ?></strong><small>Game phù hợp bộ lọc</small></div>
      </article>
    </section>

    <form method="POST" action="/admin/products" class="adminProductForm">
      <input type="hidden" name="admin_action" value="save">
      <input type="hidden" name="id" value="<?= $formId ?>">

      <div class="adminPanelHeader">
        <div>
          <h2 class="adminFormHeading"><?= $editingProduct ? 'Chỉnh sửa sản phẩm #' . $formId : 'Thêm sản phẩm mới' ?></h2>
          <p>Điền thông tin để cập nhật danh mục cửa hàng.</p>
        </div>
        <?php if ($editingProduct): ?>
          <a href="/admin/products" class="adminButton adminButtonSecondary">Hủy chỉnh sửa</a>
        <?php endif; ?>
      </div>

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
        <div class="adminField adminFieldFull">
          <label for="prodDesc">Mô tả chi tiết</label>
          <textarea id="prodDesc" name="description" rows="3"><?= $formDesc ?></textarea>
        </div>
      </div>

      <div class="adminFormActions adminFormFooter">
        <span class="adminFormHint">Các trường tên game và giá gốc là bắt buộc.</span>
        <button type="submit" class="adminButton adminButtonPrimary">
          <?= $editingProduct ? 'Lưu thay đổi' : 'Thêm sản phẩm' ?>
        </button>
      </div>
    </form>

    <section class="catalogSection adminCatalogPanel" aria-label="Danh sách sản phẩm quản trị" data-page-size="12">
      <div class="adminPanelHeader adminCatalogHeader">
        <div>
          <h2 class="adminFormHeading">Danh sách sản phẩm</h2>
          <p>Quản lý thông tin, giá và trạng thái từng game.</p>
        </div>
      </div>

      <form method="GET" action="/admin/products" class="adminSearchForm" role="search">
        <label class="adminSearchBox">
          <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m15.5 15.5 4.2 4.2"/></svg>
          <input
            type="search"
            name="q"
            value="<?= $adminSearchEscaped ?>"
            class="adminSearchInput"
            data-catalog-search
            placeholder="Tìm mã, tên, mô tả, tag, giá hoặc điểm đánh giá..."
            aria-label="Tìm kiếm sản phẩm quản trị"
          >
        </label>
        <button type="submit" class="adminButton adminButtonSecondary">Tìm kiếm</button>
      </form>

      <div class="adminTableWrap">
        <table class="adminTable">
          <thead>
            <tr>
              <th scope="col">Sản phẩm</th>
              <th scope="col">Giá bán</th>
              <th scope="col">Giảm giá</th>
              <th scope="col">Đánh giá</th>
              <th scope="col"><span class="visuallyHidden">Thao tác</span></th>
            </tr>
          </thead>
          <tbody data-catalog-list>
            <?php foreach ($products as $item): ?>
              <?php
              $rawId = (string) ($item['game_id'] ?? $item['id'] ?? '');
              $id = htmlspecialchars($rawId, ENT_QUOTES, 'UTF-8');
              $rawName = (string) ($item['name'] ?? 'Sản phẩm');
              $name = htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8');
              $initial = htmlspecialchars(mb_substr($rawName, 0, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8');
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
              <tr class="adminProductRow" data-catalog-item data-search="<?= $searchText ?>" <?= isset($adminMatchedIds[$rawId]) ? '' : 'hidden' ?>>
                <td>
                  <div class="adminProductIdentity">
                    <span class="adminProductImage">
                      <?php if ($imgSrc !== ''): ?>
                        <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy">
                      <?php else: ?>
                        <span class="adminProductImageFallback" aria-hidden="true"><?= $initial ?></span>
                      <?php endif; ?>
                    </span>
                    <span class="adminProductInfo">
                      <strong><?= $name ?></strong>
                      <small>#<?= $id ?></small>
                      <?php if ($summary !== ''): ?><span class="adminProductSummary"><?= $summary ?></span><?php endif; ?>
                      <?php if ($tags !== []): ?>
                        <span class="adminProductTags">
                          <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                            <span><?= htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8') ?></span>
                          <?php endforeach; ?>
                        </span>
                      <?php endif; ?>
                    </span>
                  </div>
                </td>
                <td>
                  <strong class="adminPrice"><?= $priceText ?></strong>
                  <?php if ($discount > 0): ?><small class="adminBasePrice"><?= $basePriceText ?></small><?php endif; ?>
                </td>
                <td>
                  <?php if ($discount > 0): ?>
                    <span class="adminStatusPill is-discount">-<?= rtrim(rtrim(number_format($discount, 2, ',', '.'), '0'), ',') ?>%</span>
                  <?php else: ?>
                    <span class="adminStatusPill is-neutral">Không</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="adminRating">
                    <?php if ($rating !== null): ?>★ <?php endif; ?>
                    <span><?= htmlspecialchars($ratingText, ENT_QUOTES, 'UTF-8') ?></span>
                  </span>
                </td>
                <td>
                  <div class="adminRowActions">
                    <a href="/admin/products?edit=<?= $id ?>" class="adminButton adminButtonSmall adminButtonSecondary">Sửa</a>
                    <form method="POST" action="/admin/products" class="inlineDeleteForm" onsubmit="return confirm('Xóa sản phẩm này?');">
                      <input type="hidden" name="admin_action" value="delete">
                      <input type="hidden" name="id" value="<?= $id ?>">
                      <button type="submit" class="adminButton adminButtonSmall adminButtonDanger">Xóa</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="emptyRow adminEmptyState" data-catalog-empty <?= count($adminSearchMatches) === 0 ? '' : 'hidden' ?>>
        <?= empty($products) ? 'Chưa có sản phẩm nào.' : 'Không tìm thấy sản phẩm nào.' ?>
      </div>
      <div class="catalogPagination" data-catalog-pagination hidden>
        <span data-catalog-page-status></span>
        <button type="button" class="adminButton adminButtonSecondary" data-catalog-show-more>Xem thêm</button>
        <button type="button" class="adminButton adminButtonSecondary" data-catalog-collapse hidden>Thu gọn</button>
      </div>
    </section>
  <?php endif; ?>
    </div>
  </div>
</section>