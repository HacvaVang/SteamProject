<?php
$loginError = (string) ($loginError ?? ($error ?? ''));
$oldUsername = (string) ($oldUsername ?? ($_POST['uname'] ?? ''));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập - SteamProject</title>
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/layout.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/auth.css">
</head>
<body>
  <main class="loginPage">
    <form id="loginForm" action="/login" method="post" class="loginCard">
      <h1 class="loginTitle">Đăng nhập</h1>

      <?php if ($loginError !== ''): ?>
        <p class="loginError" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
      <?php endif; ?>

      <div class="loginField">
        <label for="uname">Tên tài khoản</label>
        <input
          id="uname"
          type="text"
          name="uname"
          value="<?= htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Nhập tên tài khoản"
          autocomplete="username"
          required
        >
      </div>

      <div class="loginField">
        <label for="psw">Mật khẩu</label>
        <input
          id="psw"
          type="password"
          name="psw"
          placeholder="Nhập mật khẩu"
          autocomplete="current-password"
          required
        >
      </div>

      <div class="loginActions">
        <button type="submit" class="loginSubmit">Đăng nhập</button>
        <div class="loginLinks">
          <a href="/register">Chưa có tài khoản? Đăng ký</a>
        </div>
      </div>
    </form>
  </main>
</body>
</html>
