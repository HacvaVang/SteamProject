<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="register-stylesheet.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="register-js.js"></script>

        <nav class="d-flex navbar navbar-expand-md darkNav navbar-dark">
            
        <div class="mx-auto bg">
            <a href="../Home/home.html"><img
                class="logoBig" src="/public/assets/images/utils/logo_steam.svg"></a>            
        </div>

        <button type="button" class="d-none d-md-block btn btn-success" data-target="#">
            <img width="15px" height="15px" src="/public/assets/images/utils/download.png">Install Steam</button>

          </nav>
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
          #registerForm input[type="text"],
          #registerForm input[type="password"] {
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
        </style>
    </head>

    <body id="transparentBG" style="background-color: #1b2838;" class="fade-in">
      
        <img style="position: absolute; width: 800px; height:700px;visibility: hidden;" src="../../ImageData/games.jpg">
  
        <form id="registerForm" action="../Home/home.html" method="post" class="container fade-in">
          <div class="container" style="text-align: center;">
            <img src="/assets/images/utils/logo_steam.svg">
          </div>
        
          <div class="container smallFont">
            <label for="username"><b>Username</b></label>
            <input style="background-color: #e8f0fe;" type="text" placeholder="Enter Username" name="username" required>

            <label for="email"><b>Email</b></label>
            <input style="background-color: #e8f0fe;" type="text" placeholder="Enter Email" name="email" required>

            <label for="phone"><b>Phone</b></label>
            <input style="background-color: #e8f0fe;" type="text" placeholder="Enter Phone" name="phone" required>
        
            <label for="password"><b>Password</b></label>
            <input style="background-color:#e8f0fe;" type="password" placeholder="Enter Password" name="password" required>



                
            <button type="submit" class="smallFont" style="height: 50px; background-image: linear-gradient(to right, #3786c6 , #223e87);;">
              <span style="color: whitesmoke; font-weight: 600;">Sign Up</span></button>
          </div>
        
          <div class="container" >
            <button style="height: 50px; background-image: linear-gradient(to right, #df1b1b, #ba3030);" 
            type="button" class="cancelbtn smallFont" onclick="clearInput()"><span style="color: whitesmoke; font-weight: 600;">Clear</span></button>
          </div>
        </form>

    </body>
    <br>
    
    <div class="card-footer" style="background-color: #313234; width: 100%; height:100px; padding: 20px 350px; color: whitesmoke;
      overflow: hidden; position: relative;">
        <div style="float: left; padding-right: 10px; height: 100%;">
          <img  src="https://store.cloudflare.steamstatic.com/public/images/footerLogo_valve_new.png" width="88px" height="25px" alt="Valve Software">
        </div>
        <div style="float: right; padding-left: 10px; height: 100%;">
          <img  src="https://store.cloudflare.steamstatic.com/public/images/v6/logo_steam_footer.png" alt="Valve Software" width="92px" height="26px">
        </div>
      <span>
        © 2023 Valve Corporation. All rights reserved. All trademarks are property of their respective owners in the US and other countries
        <br>
        VAT included in all prices where applicable.
      </span>
      </div>
</html>