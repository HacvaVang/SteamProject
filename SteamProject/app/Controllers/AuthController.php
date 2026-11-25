<?php

class AuthController
{
    // Display login page
    public function showLogin(): void
    {
        ob_start();
        require BASE_PATH . '/app/Views/auth/login.php';
        $content = ob_get_clean();
        $pageTitle = 'SteamProject - Đăng nhập';
        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    // Display registration page
    public function showRegister(): void
    {
        ob_start();
        require BASE_PATH . '/app/Views/auth/register.php';
        $content = ob_get_clean();
        $pageTitle = 'SteamProject - Đăng kí';
        require BASE_PATH . '/app/Views/layouts/main.php';
    }

    // Handle login request
    public function login(): void
    {
        http_response_code(501);
        echo "Registration processing is not implemented.";
    }

    // Handle registration request
    public function register(): void
    {
        include '../database/db_connect.php';

        $message = "";
        $toastClass = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $phonenumber = $_POST['phone'];
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists
            $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
            $checkEmailStmt->bind_param("s", $email);
            $checkEmailStmt->execute();
            $checkEmailStmt->store_result();

            // Check if username already exists.

            if ($checkEmailStmt->num_rows > 0) {
                $message = "Email ID already exists";
                $toastClass = "#007bff"; // Primary color
            } else {
                // Prepare and bind
                $stmt = $conn->prepare("INSERT INTO userdata (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $message = "Account created successfully";
                    $toastClass = "#28a745"; // Success color
                } else {
                    $message = "Error: " . $stmt->error;
                    $toastClass = "#dc3545"; // Danger color
                }

                $stmt->close();
            }

            $checkEmailStmt->close();
            $conn->close();
        }
    }

    // Handle logout request
    public function logout(): void
    {
        http_response_code(501);
        echo "Logout processing is not implemented.";
    }
}

?>