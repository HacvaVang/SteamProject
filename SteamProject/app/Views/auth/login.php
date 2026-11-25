<?php
$styles = [ASSET_URL . '/assets/css/login.css'];
$scripts = [];
?>

<main class="login-wrapper" style="--login-bg: url('<?= htmlspecialchars(ASSET_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/utils/login_bg.jpg');">
    <form id="loginForm" action="/login" method="post" class="login-card">
        <h1 class="login-title">Sign In</h1>

        <?php if ($loginError !== ''): ?>
            <p class="login-error" role="alert"><?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="uname" class="login-label">Sign in with account name</label>
            <input id="uname" type="text" class="form-control login-input" placeholder="Enter Username" name="uname" value="<?= htmlspecialchars($oldUsername, ENT_QUOTES, 'UTF-8') ?>" required autocomplete="username">
        </div>

        <div class="form-group">
            <label for="psw" class="login-label">Password</label>
            <input id="psw" type="password" class="form-control login-input" placeholder="Enter Password" name="psw" required autocomplete="current-password">
        </div>

        <div class="login-actions">
            <button type="submit" class="btn btn-login">Sign in</button>
            <div class="login-links">
                <a href="/register">Not Registered?</a>
                <span class="link-divider">•</span>
                <a href="/register">Forgot Password?</a>
            </div>
        </div>
    </form>
</main>