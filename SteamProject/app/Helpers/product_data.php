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
                'game_id'     => (string) $idx,
                'name'        => ucfirst(str_replace(['-', '_'], ' ', $stem)),
                'summary'     => 'Sản phẩm mẫu tạo tự động từ file ' . $file,
                'description' => 'Thông tin chi tiết cho ' . ucfirst(str_replace(['-', '_'], ' ', $stem)) . ' (' . $file . ').',
                'base_price'  => $idx * 100000,
                'discount_check' => 0,
                'tags'        => [],
                'reviews'     => [],
                'image'       => $file,
            ];
            $idx++;
        }
    }

    return $items;
}

function normalizeStoreProduct(array $item): ?array
{
    $id = trim((string) ($item['game_id'] ?? $item['id'] ?? ''));
    $name = trim((string) ($item['name'] ?? ''));
    if ($id === '' || $name === '') {
        return null;
    }

    $basePrice = max(0, (int) ($item['base_price'] ?? $item['price'] ?? 0));
    $discountRaw = $item['discount_check'] ?? 0;
    $discount = is_numeric($discountRaw) ? (float) $discountRaw : 0.0;
    $discount = min(100.0, max(0.0, $discount));
    $finalPrice = (int) round($basePrice * (1 - ($discount / 100)));

    $rawTags = $item['tags'] ?? [];
    if (is_string($rawTags)) {
        $rawTags = explode(',', $rawTags);
    }
    $tags = [];
    if (is_array($rawTags)) {
        foreach ($rawTags as $tag) {
            if (!is_scalar($tag)) {
                continue;
            }
            $tag = trim((string) $tag);
            if ($tag !== '' && !in_array($tag, $tags, true)) {
                $tags[] = $tag;
            }
        }
    }

    $reviews = [];
    $rawReviews = is_array($item['reviews'] ?? null) ? $item['reviews'] : [];
    foreach ($rawReviews as $review) {
        $reviewRating = is_array($review) ? ($review['rating'] ?? null) : $review;
        if (!is_numeric($reviewRating)) {
            continue;
        }
        $reviewRating = (float) $reviewRating;
        if ($reviewRating < 0 || $reviewRating > 5) {
            continue;
        }
        $reviews[] = ['rating' => $reviewRating];
    }

    $rating = null;
    if ($reviews !== []) {
        $rating = round(
            array_sum(array_column($reviews, 'rating')) / count($reviews),
            1
        );
    }

    return [
        'game_id'        => $id,
        'id'             => $id,
        'name'           => $name,
        'summary'        => trim((string) ($item['summary'] ?? '')),
        'description'    => trim((string) ($item['description'] ?? ($item['summary'] ?? ''))),
        'base_price'     => $basePrice,
        'discount_check' => $discount,
        'final_price'    => $finalPrice,
        'price'          => $finalPrice,
        'tags'           => $tags,
        'reviews'        => $reviews,
        'rating'         => $rating,
        'review_count'   => count($reviews),
        'image'          => trim((string) ($item['image'] ?? '')),
    ];
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
        $product = normalizeStoreProduct($item);
        if ($product === null) {
            continue;
        }
        $normalized[] = [
            'game_id'        => $product['game_id'],
            'name'           => $product['name'],
            'summary'        => $product['summary'],
            'description'    => $product['description'],
            'base_price'     => $product['base_price'],
            'discount_check' => $product['discount_check'],
            'tags'           => $product['tags'],
            'reviews'        => $product['reviews'],
            'image'          => $product['image'],
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
        $normalized = [];
        foreach ($incomingProducts as $item) {
            if (is_array($item)) {
                $product = normalizeStoreProduct($item);
                if ($product !== null) {
                    $normalized[] = $product;
                }
            }
        }
        return $normalized;
    }

    $storeFile = getProductsStoreFile();
    if (is_file($storeFile)) {
        $raw = file_get_contents($storeFile);
        if ($raw !== false && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $normalized = [];
                foreach ($decoded as $item) {
                    if (is_array($item)) {
                        $product = normalizeStoreProduct($item);
                        if ($product !== null) {
                            $normalized[] = $product;
                        }
                    }
                }
                return $normalized;
            }
        }
    }

    $scanned = scanProductsFromImages();
    if (!empty($scanned)) {
        saveStoreProducts($scanned);
    }

    $normalized = [];
    foreach ($scanned as $item) {
        $product = normalizeStoreProduct($item);
        if ($product !== null) {
            $normalized[] = $product;
        }
    }
    return $normalized;
}

function findStoreProductById(array $products, string $id): ?array
{
    foreach ($products as $item) {
        if ((string) ($item['game_id'] ?? $item['id'] ?? '') === $id) {
            return $item;
        }
    }
    return null;
}

function generateNextProductId(array $products): string
{
    $maxNum = 0;
    foreach ($products as $item) {
        $id = (string) ($item['game_id'] ?? $item['id'] ?? '');
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