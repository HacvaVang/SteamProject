<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Steam news and announcements forum.">
  <title>Steam Community :: Hub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= ASSET_URL ?>/assets/css/news.css">
  <style>body{background:#1b2838;color:#c6d4df;font-family:Arial,Helvetica,sans-serif}a{color:#67c1f5}</style>
</head>
<body data-page="list">
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
      <ul id="catTabs" class="detailTabs" aria-label="Content types"></ul>
    </div>

    <div id="hubContent"></div>
  </main>

  <div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="postForm" class="modal-content" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="postModalTitle">Create a post</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <label for="postKind">Type</label>
          <select id="postKind" class="form-control mb-2">
            <option value="Discussion">Discussion</option>
            <option value="Artwork">Artwork</option>
            <option value="Screenshot">Screenshot</option>
            <option value="Video">Video</option>
            <option value="Guide">Guide</option>
            <option value="Review">Review</option>
          </select>
          <div id="reviewFields" hidden>
            <label for="postRec">Do you recommend it?</label>
            <select id="postRec" class="form-control mb-2"><option value="yes">Recommended</option><option value="no">Not recommended</option></select>
            <label for="postHours">Hours played</label>
            <input id="postHours" type="number" min="0" max="99999" step="0.1" value="0" class="form-control mb-2">
          </div>
          <label for="postTitle">Title</label>
          <input id="postTitle" type="text" class="form-control mb-2" maxlength="100" placeholder="5-100 characters">
          <label for="postBody">Description</label>
          <textarea id="postBody" class="form-control mb-2" rows="4" maxlength="1000" placeholder="10-1000 characters"></textarea>
          <label for="postImage">Image (optional, max 2 MB)</label>
          <input id="postImage" type="file" class="form-control-file" accept="image/png,image/jpeg,image/gif">
          <p id="postError" class="errorText" role="alert"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Post</button>
        </div>
      </form>
    </div>
  </div>

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
