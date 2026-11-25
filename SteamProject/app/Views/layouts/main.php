
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Helpers/product_data.php';

$requestPath = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';
if (strpos($requestPath, '/public/') === 0) {
    $requestPath = substr($requestPath, strlen('/public'));
}
$routeNames = [
    '/' => 'home',
    '/home' => 'home',
    '/products' => 'products',
    '/product' => 'product',
    '/cart' => 'cart',
    '/login' => 'login',
    '/register' => 'register',
    '/admin/products' => 'admin_products',
    '/news' => 'news',
    '/news/detail' => 'news_detail',
    '/about' => 'about',
    '/forums' => 'forums',
    '/faq' => 'faq',
    '/user' => 'user',
];
$currRoute = $routeNames[$requestPath] ?? 'home';

$isActive = static function (string $route) use ($currRoute): bool {
    return $currRoute === $route;
};

$loggedInUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isLoggedIn = !empty($loggedInUser) || !empty($_SESSION['force_user']);
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
$userHref = BASE_URL . ($isLoggedIn ? '/user' : '/login');
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
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/product.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/responsive.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/news.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/layout.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/register.css">

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
          href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/admin/products"
          class="modeSwitchBtn <?= $isActive('admin_products') ? 'is-active' : '' ?>"
        >
          Admin Sản phẩm
        </a>

        <a
          href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/products"
          class="modeSwitchBtn <?= $isActive('products') ? 'is-active' : '' ?>"
        >
          Store Sản phẩm
        </a>

        <form method="POST" action="<?= htmlspecialchars(BASE_URL, ENT_QUOTES, 'UTF-8') ?>/mode" class="modeSwitchForm">
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
          <?php if ($isLoggedIn): ?>
            <button type="submit" name="switch_mode" value="guest" class="modeSwitchToggleBtn">
              Chuyển sang Guest (không đăng nhập)
            </button>
          <?php endif; ?>
        </form>
      </div>
    </nav>
  </div>
  <?php include_once __DIR__ . '/footer.html'; ?>

  <script src="<?= ASSET_URL ?>/assets/js/main.js" defer></script>
  <script src="<?= ASSET_URL ?>/assets/js/navbar.js" defer></script>
  <script src="<?= ASSET_URL ?>/assets/js/toolbar.js" defer></script>
  <script src="<?= ASSET_URL ?>/assets/js/product.js" defer></script>
  <script src="<?= ASSET_URL ?>/assets/js/news.js" defer></script>
  <script src="<?= ASSET_URL ?>/assets/js/register.js" defer></script>
  
</body>
</html>