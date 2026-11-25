<?php
$styles = [ASSET_URL . '/assets/css/register.css'];
$scripts = [ASSET_URL . '/assets/js/register.js'];
?>

<h1 class="register-title">Create Account</h1>

<main class="register-wrapper" style="--register-bg: url('<?= htmlspecialchars(ASSET_URL, ENT_QUOTES, 'UTF-8') ?>/assets/images/utils/login_bg.jpg');">
    <form id="registerForm" action="/register" method="post" class="register-card" novalidate>
        <div class="form-group">
            <label for="username" class="register-label">Username</label>
            <input type="text" id="username" name="username" class="form-control register-input" placeholder="Enter Username" autocomplete="username" minlength="3" required>
            <span id="errUsername" class="error-message" role="alert"></span>
        </div>

        <div class="form-group">
            <label for="email" class="register-label">Email</label>
            <input type="email" id="email" name="email" class="form-control register-input" placeholder="Enter Email" autocomplete="email" required>
            <span id="errEmail" class="error-message" role="alert"></span>
        </div>

        <div class="form-group">
            <label for="phone" class="register-label">Phone</label>
            <input type="tel" id="phone" name="phone" class="form-control register-input" placeholder="Enter Phone" autocomplete="tel" inputmode="numeric" maxlength="10" required>
            <span id="errPhone" class="error-message" role="alert"></span>
        </div>

        <div class="form-group">
            <label for="password" class="register-label">Password</label>
            <input type="password" id="password" name="password" class="form-control register-input" placeholder="Enter Password" autocomplete="new-password" minlength="8" required>
            <span id="errPassword" class="error-message" role="alert"></span>
        </div>

        <div class="register-actions">
            <button type="submit" class="btn register-submit">Sign Up</button>
            <button type="button" class="btn register-clear" onclick="clearInput()">Clear</button>
        </div>

        <p class="register-login-link">Already have an account? <a href="/login">Sign in</a></p>
    </form>
</main>