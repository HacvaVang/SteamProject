<?php
$safeTitle = htmlspecialchars((string) ($pageTitle ?? 'SteamProject Admin'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $safeTitle ?></title>
    <link rel="stylesheet" href="/public/assets/css/admin-layout.css">
    <?php foreach ($vendorStyles ?? [] as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <?php foreach ($styles ?? [] as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</head>
<body class="adminPage">
    <?= $content ?? '' ?>
    <?php foreach ($scripts ?? [] as $js): ?>
        <script src="<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>
