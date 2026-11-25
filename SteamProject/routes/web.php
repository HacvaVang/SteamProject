<?php

require_once BASE_PATH . '/app/Controllers/HomeController.php';
require_once BASE_PATH . '/app/Controllers/AuthController.php';

$authController = new AuthController();

$renderView = static function (string $view, string $title): void {
    $content = '';
    ob_start();
    require BASE_PATH . '/app/Views/' . $view;
    $viewOutput = ob_get_clean();
    if ($content === '') {
        $content = $viewOutput;
    }
    $pageTitle = $title;
    require BASE_PATH . '/app/Views/layouts/main.php';
};

$renderStandaloneView = static function (string $view): void {
    require BASE_PATH . '/app/Views/' . $view;
};

$router->get('/', static function () use ($renderView): void {
    $renderView('home/index.php', 'SteamProject - Trang chủ');
});
$router->get('/home', static function () use ($renderView): void {
    $renderView('home/index.php', 'SteamProject - Trang chủ');
});

$router->get('/about', static function () use ($renderView): void {
    $renderView('about/index.php', 'SteamProject - Về Chúng Tôi');
});

$router->get('/faq', static function () use ($renderView): void {
    $renderView('faq/index.php', 'SteamProject - Những câu hỏi thường được hỏi');
});

$router->get('/faq/detail', static function () use ($renderView): void {
    $renderView('faq/detail.php', 'SteamProject - Những câu hỏi thường được hỏi');
});

$router->get('/qa', static function () use ($renderView): void {
    $renderView('qa/index.php', 'SteamProject - Hỏi Đáp');
});

$router->get('/qa/detail', static function () use ($renderView): void {
    $renderView('qa/detail.php', 'SteamProject - Hỏi Đáp');
});


$router->get('/products', static function () use ($renderView): void {
    $renderView('products/index.php', 'SteamProject - Sản phẩm');
});
$router->get('/product', static function () use ($renderView): void {
    $renderView('products/detail.php', 'SteamProject - Chi tiết sản phẩm');
});
$router->get('/cart', static function () use ($renderView): void {
    $renderView('orders/index.php', 'SteamProject - Giỏ hàng');
});
$router->get('/admin/products', static function () use ($renderView): void {
    $renderView('admin/products/index.php', 'SteamProject - Quản lý sản phẩm');
});
$router->post('/admin/products', static function () use ($renderView): void {
    $renderView('admin/products/index.php', 'SteamProject - Quản lý sản phẩm');
});

$router->get('/news', static function () use ($renderStandaloneView): void {
    $renderStandaloneView('news/index.php');
});
$router->get('/news/detail', static function () use ($renderStandaloneView): void {
    $renderStandaloneView('news/detail.php');
});

// These sections do not have dedicated views yet; retain the previous home fallback.
// foreach (['/forums', '/user', '/review'] as $path) {
//     $router->get($path, static function () use ($renderView): void {
//         $renderView('home/index.php', 'SteamProject - Trang chủ');
//     });
// }

$router->get('/login', [$authController, 'showLogin']);
$router->post('/login', [$authController, 'login']);

$router->get('/register', [$authController, 'showRegister']);
$router->post('/register', [$authController, 'register']);

$router->post('/logout', [$authController, 'logout']);

$router->post('/mode', static function (): void {
    $targetMode = (string) ($_POST['switch_mode'] ?? '');
    $returnPage = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($_POST['return_page'] ?? 'home'));
    $returnPaths = [
        'home' => BASE_URL . '/',
        'products' => BASE_URL . '/products',
        'product' => BASE_URL . '/product',
        'cart' => BASE_URL . '/cart',
        'login' => BASE_URL . '/login',
        'register' => BASE_URL . '/register',
        'admin_products' => BASE_URL . '/admin/products',
        'news' => BASE_URL . '/news',
        'news_detail' => BASE_URL . '/news/detail',
        'about' => BASE_URL . '/about',
        'forums' => BASE_URL . '/forums',
        'faq' => BASE_URL . '/faq',
        'user' => BASE_URL . '/user',
        'qa' => BASE_URL . '/qa',
    ];
    $returnPath = $returnPaths[$returnPage] ?? BASE_URL . '/';

    if ($targetMode === 'guest') {
        unset($_SESSION['user'], $_SESSION['force_user']);
        if (in_array($returnPage, ['admin_products', 'cart', 'user'], true)) {
            $returnPath = BASE_URL . '/products';
        }
    } elseif ($targetMode === 'grant_admin') {
        unset($_SESSION['force_user']);
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            $_SESSION['user'] = [
                'username' => 'admin',
                'name' => 'Admin',
                'role' => 'admin',
            ];
        } else {
            $_SESSION['user']['role'] = 'admin';
        }
        $returnPath = BASE_URL . '/admin/products';
    } elseif ($targetMode === 'revoke_admin') {
        $_SESSION['force_user'] = true;
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            $_SESSION['user'] = [
                'username' => 'user',
                'name' => 'User',
                'role' => 'user',
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
        if ($returnPage === 'admin_products') {
            $returnPath = BASE_URL . '/products';
        }
    }

    header('Location: ' . $returnPath);
    exit;
});