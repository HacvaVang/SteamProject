<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Read the discussion.">
  <title>Steam Community :: Hub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../../public/assets/css/news.css">
  <style>body{background:#1b2838;color:#c6d4df;font-family:Arial,Helvetica,sans-serif}a{color:#67c1f5}</style>
</head>
<body data-page="article">
  <nav class="navbar navbar-expand-md navbar-dark darkNav">
    <a class="navbar-brand" href="../home/index.php"><img class="logoBig" src="../../../public/assets/images/utils/logo_steam.svg" alt="Steam"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="../home/index.php">Store</a></li>
        <li class="nav-item active"><a class="nav-link" href="index.php">Community</a></li>
        <li class="nav-item"><a class="nav-link" href="../about/index.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="../contact/index.php">Support</a></li>
      </ul>
      <a class="btn btn-success" href="../auth/login.php">Sign in</a>
    </div>
  </nav>

  <main class="container forumWrap">
    <div class="hubHead">
      <div class="hubHeadTop">
        <h1 id="gameTitle">Hub</h1>
        <a class="btn btn-storepage" href="../products/index.php">Store Page</a>
      </div>
      <ul id="detailTabs" class="detailTabs"></ul>
    </div>
    <div id="crumbs" class="crumbs"></div>

    <div id="article"><p class="mutedText">Loading post...</p></div>

    <section class="comments" id="comments" aria-labelledby="commentsTitle">
      <div class="cmtHead">
        <h2 id="commentsTitle"><span id="commentCount">0</span> Comments</h2>
        <span id="commentPager" class="pager"></span>
      </div>
      <div id="commentList"></div>

      <form id="commentForm" class="replyBox" novalidate>
        <label for="commentText">Add a comment</label>
        <textarea id="commentText" class="form-control" rows="3" maxlength="500" placeholder="Add a comment (3-500 characters)"></textarea>
        <p id="commentError" class="errorText" role="alert"></p>
        <button type="submit" class="btn btn-success">Post comment</button>
        <span class="mutedText ml-2">Only signed-in members can comment.</span>
      </form>
    </section>
  </main>

  <footer class="siteFooter">
    <div class="footerInner">
      <div class="footerTop">
        <img src="https://store.cloudflare.steamstatic.com/public/images/footerLogo_valve_new.png" width="96" height="27" alt="Valve Software">
        <p>&copy; 2026 Valve Corporation. All rights reserved. All trademarks are property of their respective owners in the US and other countries.<br>VAT included in all prices where applicable.</p>
        <img class="footerSteam" src="https://store.cloudflare.steamstatic.com/public/images/v6/logo_steam_footer.png" width="92" height="26" alt="Steam">
      </div>
      <ul class="footerLinks">
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Legal</a></li>
        <li><a href="#">Steam Subscriber Agreement</a></li>
        <li><a href="#">Refunds</a></li>
        <li><a href="#">Cookies</a></li>
      </ul>
      <ul class="footerLinks footerAbout">
        <li><a href="../about/index.php">About</a></li>
        <li><a href="../contact/index.php">Support</a></li>
        <li><a href="../faq/index.php">FAQ</a></li>
        <li><a href="index.php">Community</a></li>
      </ul>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/js/news.js"></script>
</body>
</html>
