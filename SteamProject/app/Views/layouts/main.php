
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Helpers/product_data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['switch_mode'])) {
    $targetMode = (string) $_POST['switch_mode'];
    $returnPage = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($_POST['return_page'] ?? ($_GET['page'] ?? 'home')));
    if ($returnPage === '') {
        $returnPage = 'home';
    }

    if ($targetMode === 'grant_admin') {
        unset($_SESSION['force_user']);
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            $_SESSION['user'] = [
                'username' => 'admin',
                'name'     => 'Admin',
                'role'     => 'admin',
            ];
        } else {
            $_SESSION['user']['role'] = 'admin';
        }
        header('Location: ?page=admin_products');
        exit;
    }

    if ($targetMode === 'revoke_admin') {
        $_SESSION['force_user'] = true;
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            $_SESSION['user'] = [
                'username' => 'user',
                'name'     => 'User',
                'role'     => 'user',
            ];
        } else {
            $_SESSION['user']['role'] = 'user';
            if (strtolower((string) ($_SESSION['user']['username'] ?? '')) === 'admin') {
                $_SESSION['user']['username'] = 'user';
            }
            if (strtolower((string) ($_SESSION['user']['name'] ?? '')) === 'admin') {
                $_SESSION['user']['name'] = 'User';
            }
        }
        $nextPage = ($returnPage === 'admin_products') ? 'products' : $returnPage;
        header('Location: ?page=' . urlencode($nextPage));
        exit;
    }
}

$currRoute = $_GET['page'] ?? trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($currRoute === '' || $currRoute === 'index.php') {
    $currRoute = 'home';
}

$isActive = static function (string $route) use ($currRoute): bool {
    return $currRoute === $route;
};

$loggedInUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isLoggedIn = !empty($loggedInUser);
$isAdmin = isAdminUser($loggedInUser);

$userLabel = $isLoggedIn
    ? htmlspecialchars(
        (string) (is_array($loggedInUser)
            ? ($loggedInUser['name'] ?? $loggedInUser['username'] ?? 'User')
            : $loggedInUser),
        ENT_QUOTES,
        'UTF-8'
    )
    : 'Đăng nhập';
$userHref = $isLoggedIn ? '?page=user' : '?page=login';
$userRouteKey = $isLoggedIn ? 'user' : 'login';
$safeTitle = htmlspecialchars((string) ($pageTitle ?? 'SteamProject'), ENT_QUOTES, 'UTF-8');
$safeCurrRoute = htmlspecialchars($currRoute, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'SteamProject') ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/product.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/news.css">
  <link rel="stylesheet" href="assets/css/layout.css">
  <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
  <?php require __DIR__ . '/header.php'; ?>

  <main class="content-wrap">
    <?= $content ?? '' ?>
  </main>
  <div class="globalSwitchWrap">
    <nav class="modeSwitchBar" aria-label="Chuyển đổi chế độ Admin / Store">
      <span class="modeSwitchLabel">
        Chế độ hiện tại: <strong><?= $isAdmin ? 'Admin' : ($isLoggedIn ? 'User thường' : 'Khách') ?></strong>
      </span>

      <div class="modeSwitchControls">
        <a
          href="?page=admin_products"
          class="modeSwitchBtn <?= $isActive('admin_products') ? 'is-active' : '' ?>"
        >
          Admin Sản phẩm
        </a>

        <a
          href="?page=products"
          class="modeSwitchBtn <?= $isActive('products') ? 'is-active' : '' ?>"
        >
          Store Sản phẩm
        </a>

        <form method="POST" action="" class="modeSwitchForm">
          <input type="hidden" name="return_page" value="<?= $safeCurrRoute ?>">
          <?php if ($isAdmin): ?>
            <button type="submit" name="switch_mode" value="revoke_admin" class="modeSwitchToggleBtn">
              Đổi sang User thường
            </button>
          <?php else: ?>
            <button type="submit" name="switch_mode" value="grant_admin" class="modeSwitchToggleBtn">
              Bật quyền Admin (test)
            </button>
          <?php endif; ?>
        </form>
      </div>
    </nav>
  </div>
  <?php include_once __DIR__ . '/footer.php'; ?>

  <script src="assets/js/main.js" defer></script>
  <script src="assets/js/navbar.js" defer></script>
  <script src="assets/js/toolbar.js" defer></script>
  <script src="assets/js/product.js" defer></script>
  <script src="assets/js/news.js" defer></script>
  
</body>
</html>