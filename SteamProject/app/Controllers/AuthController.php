<?php

class AuthController
{
    // Display login page
    public function showLogin(): void
    {
        $loginError = '';
        $oldUsername = '';
        $this->renderLogin($loginError, $oldUsername);
    }

    // Display registration page
    public function showRegister(): void
    {
        require BASE_PATH . '/app/Views/auth/register.php';
    }

    // Handle login request
    public function login(): void
    {
        http_response_code(501);
        $loginError = 'Chức năng đăng nhập chưa được triển khai.';
        $oldUsername = (string) ($_POST['uname'] ?? '');
        $this->renderLogin($loginError, $oldUsername);
    }

    // Handle registration request
    public function register(): void
    {
        http_response_code(501);
        echo "Registration processing is not implemented.";
    }

    // Handle logout request
    public function logout(): void
    {
        http_response_code(501);
        echo "Logout processing is not implemented.";
    }

    private function renderLogin(string $loginError, string $oldUsername): void
    {
        ob_start();
        require BASE_PATH . '/app/Views/auth/login.php';
        $content = ob_get_clean();
        $pageTitle = 'SteamProject - Đăng nhập';
        require BASE_PATH . '/app/Views/layouts/main.php';
    }
}

?>