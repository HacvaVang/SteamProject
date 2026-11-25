<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create Account - Steam</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <style>
          #registerForm {
            width: 100%;
            max-width: 480px;
            margin: 5vh auto 0;
            padding: 0 16px;
          }
          #registerForm label,
          #registerForm label b,
          #registerForm .smallFont {
            color: #c6d4df;
          }
          #registerForm input {
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin: 6px 0 18px;
          }
          #registerForm button[type="submit"],
          #registerForm .cancelbtn {
            display: block;
            width: 140px;
            margin: 8px auto 0;
          }
          .error-message {
              display: block;
              color: #ff4d4d;
              font-size: 12px;
              margin-bottom: 10px;
          }

          .error-message:empty {
              display: none;
          }
          .install-steam {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              gap: 8px;

              padding: 8px 12px;

              background-color: #5c7e10;
              color: #ffffff;

              font-size: 12px;
              font-weight: 500;
              text-decoration: none;
              white-space: nowrap;

              border-radius: 2px;
              transition: background-color 0.2s ease;
          }
          .form-actions {
              display: flex;
              flex-direction: row;
              gap: 10px;
          }

          .form-actions button {
              flex: 1;
              margin: 0;
              width: auto;
          }
        </style>
    </head>

    <body id="transparentBG" style="background-color: #1b2838;" class="fade-in">
        <nav class="d-flex navbar navbar-expand-md darkNav navbar-dark" aria-label="Registration navigation">
            <div class="mx-auto bg">
                <a href="/">
                    <img class="logoBig" src="<?= ASSET_URL ?>/assets/images/utils/logo_steam.svg" alt="Steam">
                </a>
            </div>
            <a class="install-steam" href="https://cdn.akamai.steamstatic.com/client/installer/SteamSetup.exe">
                <img width="15" height="15" src="<?= ASSET_URL ?>/assets/images/utils/download.png" alt="">
                Install Steam
            </a>
        </nav>

        <img style="position: absolute; width: 100%; height:100%; visibility: hidden;" src="<?= ASSET_URL ?>/assets/images/utils/login_bg.jpg">
  
      <form id="registerForm" action="/register" method="post" class="container fade-in" novalidate>

          <div class="container" style="text-align: center;">
            <img src="<?= ASSET_URL ?>/assets/images/utils/logo_steam.svg" alt="Steam Logo">
          </div>

          <div class="container smallFont">

              <!-- Username -->
              <label for="username"><b>Username</b></label>
              <input style="background-color: #e8f0fe;" type="text" placeholder="Enter Username" id="username" name="username" autocomplete="username" required>
              <span id="errUsername" class="error-message"></span>

              <!-- Email -->
              <label for="email"><b>Email</b></label>
              <input style="background-color: #e8f0fe;" type="email" placeholder="Enter Email" id="email" name="email" autocomplete="email" required>
              <span id="errEmail" class="error-message"></span>

              <!-- Phone -->
              <label for="phone"><b>Phone</b></label>
              <input style="background-color: #e8f0fe;" type="tel" placeholder="Enter Phone" id="phone" name="phone" autocomplete="tel" inputmode="numeric" maxlength="10" required>
              <span id="errPhone" class="error-message"></span>

              <!-- Password -->
              <label for="password"><b>Password</b></label>
              <input style="background-color: #e8f0fe;" type="password" placeholder="Enter Password" id="password" name="password" autocomplete="new-password" required>
              <span id="errPassword" class="error-message"></span>
          </div>

          <div class="container form-actions">
              <!-- Sign Up Button -->
              <button type="submit" class="smallFont" style="height: 50px; background-image: linear-gradient(to right, #3786c6, #223e87);">
                  <span style="color: whitesmoke; font-weight: 600;">Sign Up</span>
              </button>
              <!-- Clear Button -->              
              <button style="height: 50px; background-image: linear-gradient(to right, #df1b1b, #ba3030);" type="button" class="cancelbtn smallFont" onclick="clearInput()">
                  <span style="color: whitesmoke; font-weight: 600;">Clear</span>
              </button>
          </div>

      </form>

        <footer class="card-footer" style="background-color: #313234; width: 100%; min-height:100px; padding: 20px 350px; color: whitesmoke; overflow: hidden; position: relative;">
            <div style="float: left; padding-right: 10px; height: 100%;">
              <img src="<?= ASSET_URL ?>/assets/images/utils/valve.png" width="88" height="25" alt="Valve Software">
            </div>
            <span>
              © 2026 Valve Corporation. All rights reserved. All trademarks are property of their respective owners in the US and other countries
              <br>
              VAT included in all prices where applicable.
            </span>
        </footer>
    </body>
</html>