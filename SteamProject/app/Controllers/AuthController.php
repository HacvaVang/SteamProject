<?php

class AuthController
{
    // Display login page
    public function showLogin(): void
    {
        require BASE_PATH . '/app/Views/auth/login.php';
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
        echo "Login processing is not implemented.";
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
}

?>