<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'SteamProject') ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/product.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/news.css">
</head>
<body>
  <?php include_once __DIR__ . '/navbar.php'; ?>

  <main class="forumWrap">
    <?= $content ?? '' ?>
  </main>

  <?php include_once __DIR__ . '/footer.php'; ?>

  <script src="assets/js/main.js" defer></script>
  <script src="assets/js/navbar.js" defer></script>
  <script src="assets/js/toolbar.js" defer></script>
  <script src="assets/js/product.js" defer></script>
  <script src="assets/js/news.js" defer></script>
  
</body>
</html>