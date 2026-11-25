<?php
// Lấy route/trang hiện tại từ query hoặc URI (tùy theo Router/Controller của bạn)
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
?>
<header class="steamHeader">
  <div class="headerTop">
    <a href="/login">Đăng nhập</a>
    <a class="btn-install" href="/about">Cài đặt</a>
  </div>
  <div class="headerInner">
    <a href="/" class="logoLink">
      <img src="<?= ASSET_URL ?>/assets/images/logo/logo.svg" alt="SteamProject" class="logoBig">
    </a>
    <ul class="mainNav">
      <li class="<?= $currRoute === 'home' ? 'active' : '' ?>"><a href="/">Trang chủ</a></li>
      <li class="<?= $currRoute === 'products' ? 'active' : '' ?>"><a href="/products">Cửa hàng</a></li>
      <li class="<?= $currRoute === 'news' ? 'active' : '' ?>"><a href="/news">Tin tức</a></li>
      <li class="<?= $currRoute === 'faq' ? 'active' : '' ?>"><a href="/faq">Hỗ trợ</a></li>
    </ul>
  </div>
</header>