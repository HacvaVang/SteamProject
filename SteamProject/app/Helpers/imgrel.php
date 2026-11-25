<?php
function resolveProductImage(string $rawImage, string $productId = ''): string
{
    $projectRoot = realpath(__DIR__ . '/../..') ?: (__DIR__ . '/../..');
    $baseDir = rtrim($projectRoot, '/\\') . '/public/assets/images/products/';
    $webPrefix = '/public/assets/images/products/';
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];

    $rawImage = trim($rawImage);
    $candidates = [];

    if ($rawImage !== '') {
        $candidates[] = $rawImage;
        $stem = pathinfo($rawImage, PATHINFO_FILENAME);
        if ($stem !== '') {
            foreach ($exts as $ext) {
                $candidates[] = $stem . '.' . $ext;
            }
        }
    }

    if ($productId !== '') {
        foreach ($exts as $ext) {
            $candidates[] = $productId . '.' . $ext;
            $candidates[] = 'product-' . $productId . '.' . $ext;
            $candidates[] = 'product_' . $productId . '.' . $ext;
        }
    }

    $candidates = array_unique($candidates);

    foreach ($candidates as $file) {
        $safeFile = basename($file);
        if ($safeFile !== '' && is_file($baseDir . $safeFile)) {
            return $webPrefix . rawurlencode($safeFile);
        }
    }

    return '';
}?>