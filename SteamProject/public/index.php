<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));

define('BASE_URL', '');
define('ASSET_URL', '/public');

// Load Router
require_once BASE_PATH . '/app/Core/Router.php';

// Initialize Router
$router = new Router();

// Load routing table
require BASE_PATH . '/routes/web.php';

// Get HTTP request information
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$requestPath = (string) parse_url($uri, PHP_URL_PATH);

if ($requestPath === '/public' || strpos($requestPath, '/public/') === 0) {
    $requestPath = substr($requestPath, strlen('/public')) ?: '/';
}
if ($requestPath === '/index.php' || $requestPath === '' || $requestPath === '/') {
    $requestPath = '/';
}

$router->dispatch($method, $requestPath);
?>