<?php
$loginError = (string) ($loginError ?? ($error ?? ''));
$oldUsername = (string) ($oldUsername ?? ($_POST['uname'] ?? ''));
?>
<section class="loginPage" aria-label="Đăng nhập">
  <form id="loginForm" action="?page=login" method="post" class="loginCard">
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
        <a href="?page=register">Chưa có tài khoản? Đăng ký</a>
        <a href="?page=register">Quên mật khẩu?</a>
      </div>
    </div>
  </form>
</section>
