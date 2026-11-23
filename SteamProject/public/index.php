<?php
// Trong public/index.php (ví dụ điều hướng cơ bản theo ?page=)
$page = $_GET['page'] ?? 'home';

$viewMap = [
    'home'     => [__DIR__ . '/../app/Views/home/index.php', 'Trang chủ'],
    'products' => [__DIR__ . '/../app/Views/products/index.php', 'Sản phẩm'],
    'product'  => [__DIR__ . '/../app/Views/products/detail.php', 'Chi tiết sản phẩm'],
    'cart'     => [__DIR__ . '/../app/Views/orders/index.php', 'Giỏ hàng'],
    'login'    => [__DIR__ . '/../app/Views/auth/login.php', 'Đăng nhập'],
    'register' => [__DIR__ . '/../app/Views/auth/register.php', 'Đăng ký'],
];

[$viewFile, $pageTitle] = $viewMap[$page] ?? $viewMap['home'];

ob_start();
if (is_file($viewFile)) {
    include $viewFile;
} else {
    echo '<div class="emptyRow">Không tìm thấy view tương ứng.</div>';
}
$content = ob_get_clean();

include __DIR__ . '/../app/Views/layouts/main.php';