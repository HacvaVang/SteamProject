<?php
$styles = [ASSET_URL . '/assets/css/login.css'];
$scripts = [ASSET_URL . '/assets/js/login.css'];
?>

<h1 class="login-title">Sign In</h1>

<main class="login-wrapper" style="--login-bg: url('<?= htmlspecialchars(ASSET_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/utils/login_bg.jpg');">
    <form id="loginForm" action="/login" method="post" class="login-card">
        

        <div class="form-group">
            <label for="username" class="login-label">Sign in with account name</label>
            <input id="username" type="text" class="form-control login-input" placeholder="Enter Username" name="username" required autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password" class="login-label">Password</label>
            <input id="password" type="password" class="form-control login-input" placeholder="Enter Password" name="password" required autocomplete="current-password">
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