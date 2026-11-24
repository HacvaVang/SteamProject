<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'SteamProject') ?></title>

    <link rel="stylesheet" href="/public/assets/css/layout.css">
</head>

<body>

    <?php require __DIR__ . '/header.html'; ?>

    <main class="main-content">
        <?= $content ?? '' ?>
    </main>

    <?php require __DIR__ . '/footer.html'; ?>

</body>
</html>