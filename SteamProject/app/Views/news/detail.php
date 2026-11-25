<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Read the discussion.">
  <title>Steam Community :: Hub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/news.css">
  <style>body{background:#1b2838;color:#c6d4df;font-family:Arial,Helvetica,sans-serif}a{color:#67c1f5}</style>
</head>
<body data-page="article">
  <header class="steamHeader">
    <div class="headerTop">
      <a class="btn-install" href="#">Install Steam</a>
      <a href="/login">sign in</a>
    </div>
    <div class="headerInner">
      <a href="/"><img class="logoBig" src="<?= ASSET_URL ?>/assets/images/utils/logo_steam.svg" alt="Steam"></a>
      <nav aria-label="Main">
        <ul class="mainNav">
          <li><a href="/products">Store</a></li>
          <li class="active"><a href="index.php">Community</a></li>
          <li><a href="/about">About</a></li>
          <li><a href="/faq">Support</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container forumWrap">
    <div class="hubHead">
      <div class="hubHeadTop">
        <h1 id="gameTitle">Hub</h1>
        <a class="btn btn-storepage" href="/products">Store Page</a>
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
        <li><a href="/about">About</a></li>
        <li><a href="/faq">Support</a></li>
        <li><a href="/faq">FAQ</a></li>
        <li><a href="/news">Community</a></li>
      </ul>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= ASSET_URL ?>/assets/js/news.js"></script>
</body>
</html>
