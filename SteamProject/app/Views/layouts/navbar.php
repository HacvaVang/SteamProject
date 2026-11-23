<?php
// Lấy route/trang hiện tại từ query hoặc URI (tùy theo Router/Controller của bạn)
$currRoute = $_GET['page'] ?? trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($currRoute === '' || $currRoute === 'index.php') {
  $currRoute = 'home';
}
?>
<header class="steamHeader">
  <div class="headerTop">
    <a href="?page=login">Đăng nhập</a>
    <a class="btn-install" href="?page=about">Cài đặt</a>
  </div>
  <div class="headerInner">
    <a href="?page=home" class="logoLink">
      <img src="assets/images/logo/logo.svg" alt="SteamProject" class="logoBig">
    </a>
    <ul class="mainNav">
      <li class="<?= $currRoute === 'home' ? 'active' : '' ?>"><a href="?page=home">Trang chủ</a></li>
      <li class="<?= $currRoute === 'products' ? 'active' : '' ?>"><a href="?page=products">Cửa hàng</a></li>
      <li class="<?= $currRoute === 'news' ? 'active' : '' ?>"><a href="?page=news">Tin tức</a></li>
      <li class="<?= $currRoute === 'faq' ? 'active' : '' ?>"><a href="?page=faq">Hỗ trợ</a></li>
    </ul>
  </div>
</header>