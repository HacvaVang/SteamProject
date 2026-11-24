<?php
// Lấy route/trang hiện tại từ query hoặc URI (tùy theo Router/Controller của bạn)
$currRoute = $_GET['page'] ?? trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($currRoute === '' || $currRoute === 'index.php') {
  $currRoute = 'home';
}

$isActive = static function (string $route) use ($currRoute): bool {
  return $currRoute === $route;
};
?>
<header class="site-header">
  <nav class="navbar" aria-label="Điều hướng chính">

    <!-- Steam Logo -->
    <div class="navbar-brand">
      <a href="?page=home">
        <img
          src="assets/images/utils/logo_steam.svg"
          alt="Steam Logo"
          class="navbar-logo"
        >
      </a>
    </div>

    <!-- Navigation Menu -->
    <ul class="navbar-menu">

      <li class="navbar-item <?= $isActive('home') ? 'active' : '' ?>">
        <a href="?page=home" class="navbar-link<?= $isActive('home') ? ' is-active' : '' ?>">
          Home
        </a>
      </li>

      <li class="navbar-item <?= $isActive('products') ? 'active' : '' ?>">
        <a href="?page=products" class="navbar-link<?= $isActive('products') ? ' is-active' : '' ?>">
          Store
        </a>
      </li>

      <li class="navbar-item <?= $isActive('about') ? 'active' : '' ?>">
        <a href="?page=about" class="navbar-link<?= $isActive('about') ? ' is-active' : '' ?>">
          About
        </a>
      </li>

      <li class="navbar-item <?= $isActive('forums') ? 'active' : '' ?>">
        <a href="?page=forums" class="navbar-link<?= $isActive('forums') ? ' is-active' : '' ?>">
          Forums
        </a>
      </li>

      <?php
        $loggedInUser = $currentUser ?? ($_SESSION['user'] ?? null);
        $isLoggedIn = !empty($loggedInUser) || !empty($_SESSION['force_user']);
        $userLabel = $isLoggedIn
          ? htmlspecialchars((string) (is_array($loggedInUser) ? ($loggedInUser['name'] ?? $loggedInUser['username'] ?? 'User') : ($loggedInUser ?: 'User')), ENT_QUOTES, 'UTF-8')
          : 'Đăng nhập';
        $userHref = $isLoggedIn ? '?page=user' : '?page=login';
        $userRouteKey = $isLoggedIn ? 'user' : 'login';
      ?>
      <?php if ($isLoggedIn): ?>
        <li class="navbar-item navbar-cart <?= $isActive('cart') ? 'active' : '' ?>">
          <a href="?page=cart" class="navbar-link<?= $isActive('cart') ? ' is-active' : '' ?>">
            Giỏ hàng
          </a>
        </li>
      <?php endif; ?>
      <li
        class="navbar-item navbar-user <?= $isActive($userRouteKey) ? 'active' : '' ?>"
        data-auth-nav
        data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>"
        data-user-name="<?= $isLoggedIn ? $userLabel : '' ?>"
      >
        <a
          href="<?= $userHref ?>"
          class="navbar-link<?= $isActive($userRouteKey) ? ' is-active' : '' ?>"
          data-auth-link
        >
          <?= $userLabel ?>
        </a>
      </li>

    </ul>

  </nav>
</header>