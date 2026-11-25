<?php
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
  '/qa' => 'qa',
  '/user' => 'user',
];
$currRoute = $routeNames[$requestPath] ?? 'home';

$isActive = static function (string $route) use ($currRoute): bool {
  return $currRoute === $route;
};
?>
<header class="site-header">
  <nav class="navbar" aria-label="Điều hướng chính">

    <!-- Steam Logo -->
    <div class="navbar-brand">
      <a href="/">
        <img src="<?= ASSET_URL ?>/assets/images/utils/logo_steam.svg" alt="Steam Logo" class="navbar-logo">
      </a>
    </div>

    <!-- Navigation Menu -->
    <ul class="navbar-menu">

      <li class="navbar-item <?= $isActive('home') ? 'active' : '' ?>">
        <a href="/" class="navbar-link<?= $isActive('home') ? ' is-active' : '' ?>">
          Home
        </a>
      </li>

      <li class="navbar-item <?= $isActive('products') ? 'active' : '' ?>">
        <a href="/products" class="navbar-link<?= $isActive('products') ? ' is-active' : '' ?>">
          Store
        </a>
      </li>

      <li class="navbar-item <?= $isActive('about') ? 'active' : '' ?>">
        <a href="/about" class="navbar-link<?= $isActive('about') ? ' is-active' : '' ?>">
          About
        </a>
      </li>

      <li class="navbar-item <?= $isActive('forums') ? 'active' : '' ?>">
        <a href="/forums" class="navbar-link<?= $isActive('forums') ? ' is-active' : '' ?>">
          Forums
        </a>
      </li>

      <li class="navbar-item <?= $isActive('faq') ? 'active' : '' ?>">
        <a href="/faq" class="navbar-link<?= $isActive('faq') ? ' is-active' : '' ?>">
          FAQs
        </a>
      </li>

      <li class="navbar-item <?= $isActive('qa') ? 'active' : '' ?>">
        <a href="/qa" class="navbar-link<?= $isActive('qa') ? ' is-active' : '' ?>">
          Support
        </a>
      </li>

      <?php
        $loggedInUser = $currentUser ?? ($_SESSION['user'] ?? null);
        $isLoggedIn = !empty($loggedInUser) || !empty($_SESSION['force_user']);
        $userLabel = $isLoggedIn
          ? htmlspecialchars((string) (is_array($loggedInUser) ? ($loggedInUser['name'] ?? $loggedInUser['username'] ?? 'User') : ($loggedInUser ?: 'User')), ENT_QUOTES, 'UTF-8')
          : 'Đăng nhập';
        $userHref = $isLoggedIn ? '/user' : '/login';
        $userRouteKey = $isLoggedIn ? 'user' : 'login';
      ?>
      <?php if ($isLoggedIn): ?>
        <li class="navbar-item navbar-cart <?= $isActive('cart') ? 'active' : '' ?>">
          <a href="/cart" class="navbar-link<?= $isActive('cart') ? ' is-active' : '' ?>">
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