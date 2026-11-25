<?php
require_once __DIR__ . '/imgrel.php';

function getProductsStoreFile(): string
{
    $projectRoot = realpath(__DIR__ . '/../..') ?: (__DIR__ . '/../..');
    return rtrim($projectRoot, '/\\') . '/app/Data/products.json';
}

function scanProductsFromImages(): array
{
    $projectRoot = realpath(__DIR__ . '/../..') ?: (__DIR__ . '/../..');
    $imgDir = rtrim($projectRoot, '/\\') . '/public/assets/images/products';
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
    $items = [];

    if (is_dir($imgDir)) {
        $files = scandir($imgDir) ?: [];
        $idx = 1;
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $fullPath = $imgDir . DIRECTORY_SEPARATOR . $file;
            if (!is_file($fullPath)) {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts, true)) {
                continue;
            }
            $stem = pathinfo($file, PATHINFO_FILENAME);
            $items[] = [
                'id'          => (string) $idx,
                'name'        => ucfirst(str_replace(['-', '_'], ' ', $stem)),
                'summary'     => 'Sản phẩm mẫu tạo tự động từ file ' . $file,
                'description' => 'Thông tin chi tiết cho ' . ucfirst(str_replace(['-', '_'], ' ', $stem)) . ' (' . $file . ').',
                'price'       => $idx * 100000,
                'image'       => $file,
            ];
            $idx++;
        }
    }

    return $items;
}

function saveStoreProducts(array $products): bool
{
    $storeFile = getProductsStoreFile();
    $dir = dirname($storeFile);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $normalized = [];
    foreach ($products as $item) {
        if (!is_array($item)) {
            continue;
        }
        $id = trim((string) ($item['id'] ?? ''));
        $name = trim((string) ($item['name'] ?? ''));
        if ($id === '' || $name === '') {
            continue;
        }
        $normalized[] = [
            'id'          => $id,
            'name'        => $name,
            'summary'     => trim((string) ($item['summary'] ?? '')),
            'description' => trim((string) ($item['description'] ?? ($item['summary'] ?? ''))),
            'price'       => max(0, (int) ($item['price'] ?? 0)),
            'image'       => trim((string) ($item['image'] ?? '')),
        ];
    }

    $json = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    return file_put_contents($storeFile, $json . PHP_EOL, LOCK_EX) !== false;
}

function getStoreProducts(array $incomingProducts = []): array
{
    if (!empty($incomingProducts)) {
        return array_values($incomingProducts);
    }

    $storeFile = getProductsStoreFile();
    if (is_file($storeFile)) {
        $raw = file_get_contents($storeFile);
        if ($raw !== false && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values($decoded);
            }
        }
    }

    $scanned = scanProductsFromImages();
    if (!empty($scanned)) {
        saveStoreProducts($scanned);
    }

    return $scanned;
}

function findStoreProductById(array $products, string $id): ?array
{
    foreach ($products as $item) {
        if ((string) ($item['id'] ?? '') === $id) {
            return $item;
        }
    }
    return null;
}

function generateNextProductId(array $products): string
{
    $maxNum = 0;
    foreach ($products as $item) {
        $id = (string) ($item['id'] ?? '');
        if (ctype_digit($id)) {
            $maxNum = max($maxNum, (int) $id);
        }
    }
    return (string) ($maxNum + 1);
}

function isAdminUser(mixed $user = null): bool
{
    if (!empty($_SESSION['force_user'])) {
        return false;
    }

    $candidate = $user ?? ($_SESSION['user'] ?? null);
    if (empty($candidate)) {
        return false;
    }

    if (is_array($candidate)) {
        $role = strtolower((string) ($candidate['role'] ?? ''));
        $username = strtolower((string) ($candidate['username'] ?? ''));
        $name = strtolower((string) ($candidate['name'] ?? ''));
        return $role === 'admin' || $username === 'admin' || $name === 'admin';
    }

    return strtolower((string) $candidate) === 'admin';
}