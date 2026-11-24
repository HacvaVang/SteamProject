<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';

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
        $price = max(0, (int) ($_POST['price'] ?? 0));
        $image = trim((string) ($_POST['image'] ?? ''));
        $summary = trim((string) ($_POST['summary'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($name === '') {
            $adminError = 'Vui lòng nhập tên sản phẩm.';
        } else {
            $isEditing = $submittedId !== '' && findStoreProductById($products, $submittedId) !== null;
            $targetId = $isEditing ? $submittedId : generateNextProductId($products);

            $record = [
                'id'          => $targetId,
                'name'        => $name,
                'summary'     => $summary,
                'description' => $description !== '' ? $description : $summary,
                'price'       => $price,
                'image'       => $image,
            ];

            if ($isEditing) {
                foreach ($products as $idx => $existing) {
                    if ((string) ($existing['id'] ?? '') === $targetId) {
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
                $adminError = 'Không thể lưu dữ liệu vào file sản phẩm.';
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
                    $adminError = 'Không thể cập nhật file sau khi xóa.';
                }
            }
        }
    } elseif ($action === 'scan_images') {
        $scanned = scanProductsFromImages();
        if (saveStoreProducts($scanned)) {
            $products = $scanned;
            $adminNotice = 'Đã đồng bộ lại sản phẩm từ thư mục ảnh.';
        } else {
            $adminError = 'Không thể lưu dữ liệu quét từ ảnh.';
        }
    }
}

$editId = trim((string) ($_GET['edit'] ?? ''));
$editingProduct = ($isAdmin && $editId !== '') ? findStoreProductById($products, $editId) : null;

$formId = htmlspecialchars((string) ($editingProduct['id'] ?? ''), ENT_QUOTES, 'UTF-8');
$formName = htmlspecialchars((string) ($editingProduct['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$formPrice = (int) ($editingProduct['price'] ?? 0);
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
        <form method="POST" action="?page=admin_products" class="inlineDeleteForm" onsubmit="return confirm('Quét lại sản phẩm từ thư mục ảnh?');">
          <input type="hidden" name="admin_action" value="scan_images">
          <button type="submit" class="catalogDetailBtn">Quét lại từ ảnh</button>
        </form>
      <?php endif; ?>
      <a href="?page=products" class="catalogDetailBtn">Xem trang Store</a>
    </div>
  </header>

  <?php if (!$isAdmin): ?>
    <section class="adminLockedBox" aria-label="Yêu cầu quyền Admin">
      <h2 class="adminFormHeading">Truy cập bị hạn chế</h2>
      <p class="storeBannerSub">
        Trang <strong>Quản lý sản phẩm</strong> chỉ dành cho tài khoản có quyền <strong>admin</strong>. Bạn có thể bấm nút chuyển quyền ở thanh phía dưới để kiểm thử nhanh.
      </p>
      <div class="adminLockedActions">
        <a href="?page=login" class="catalogDetailBtn">Đăng nhập tài khoản Admin</a>
        <a href="?page=products" class="catalogAddCartBtn">Quay lại Store</a>
      </div>
    </section>
  <?php else: ?>
    <?php if ($adminNotice !== ''): ?>
      <div class="adminAlertSuccess" role="status"><?= htmlspecialchars($adminNotice, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($adminError !== ''): ?>
      <div class="adminAlertError" role="alert"><?= htmlspecialchars($adminError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST" action="?page=admin_products" class="adminProductForm">
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
          <label for="prodPrice">Giá (VNĐ)</label>
          <input id="prodPrice" type="number" name="price" min="0" step="1000" value="<?= $formPrice ?>" required>
        </div>

        <div class="adminField">
          <label for="prodImage">Tên file ảnh (trong assets/images/products)</label>
          <input id="prodImage" type="text" name="image" value="<?= $formImage ?>" placeholder="vd: league.jpeg">
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
          <a href="?page=admin_products" class="catalogDetailBtn">Hủy sửa</a>
        <?php endif; ?>
      </div>
    </form>

    <section class="catalogSection" aria-label="Danh sách sản phẩm quản trị">
      <div class="catalogTop">
        <h2 class="catalogHeading">Danh sách sản phẩm</h2>
        <span class="catalogCount" data-catalog-count><?= count($products) ?> sản phẩm</span>
      </div>

      <div class="filterForm" role="search">
        <div class="filterBox">
          <input
            type="search"
            class="filterInput"
            data-catalog-search
            placeholder="Tìm theo tên, mô tả hoặc mã sản phẩm..."
            aria-label="Tìm kiếm sản phẩm quản trị"
          >
        </div>
      </div>

      <div class="emptyRow" data-catalog-empty <?= empty($products) ? '' : 'hidden' ?>>
        <?= empty($products) ? 'Chưa có sản phẩm nào.' : 'Không tìm thấy sản phẩm nào.' ?>
      </div>
      <div class="catalogList" data-catalog-list>
        <?php foreach ($products as $item): ?>
          <?php
          $id = htmlspecialchars((string) ($item['id'] ?? ''), ENT_QUOTES, 'UTF-8');
          $name = htmlspecialchars((string) ($item['name'] ?? 'Sản phẩm'), ENT_QUOTES, 'UTF-8');
          $summary = htmlspecialchars((string) ($item['summary'] ?? ''), ENT_QUOTES, 'UTF-8');
          $searchText = htmlspecialchars(
              trim((string) ($item['id'] ?? '') . ' ' . (string) ($item['name'] ?? '') . ' ' . (string) ($item['summary'] ?? '')),
              ENT_QUOTES,
              'UTF-8'
          );
          $priceText = number_format((int) ($item['price'] ?? 0), 0, ',', '.') . ' đ';
          $imgSrc = resolveProductImage((string) ($item['image'] ?? ''), (string) ($item['id'] ?? ''));
          ?>
          <article class="catalogRow" data-catalog-item data-search="<?= $searchText ?>">
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
            </div>

            <div class="catalogAside">
              <span class="catalogPrice"><?= $priceText ?></span>
              <div class="adminRowActions">
                <a href="?page=admin_products&edit=<?= $id ?>" class="catalogDetailBtn">Sửa</a>
                <form method="POST" action="?page=admin_products" class="inlineDeleteForm" onsubmit="return confirm('Xóa sản phẩm này?');">
                  <input type="hidden" name="admin_action" value="delete">
                  <input type="hidden" name="id" value="<?= $id ?>">
                  <button type="submit" class="adminDeleteBtn">Xóa</button>
                </form>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</section>