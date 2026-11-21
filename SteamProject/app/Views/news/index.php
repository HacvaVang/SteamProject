<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Steam news and announcements forum.">
  <title>Steam Community :: Hub</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../../public/assets/css/news.css">
  <style>body{background:#1b2838;color:#c6d4df;font-family:Arial,Helvetica,sans-serif}a{color:#67c1f5}</style>
</head>
<body data-page="list">
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
    <div class="crumbs"><a href="../home/index.php">Community</a> &gt; <span>Hub</span></div>

    <header class="hubBanner">
      <div class="hubIcon" aria-hidden="true"></div>
      <div class="hubTitle"><h1 id="gameName">Hub</h1><p>Community Hub</p></div>
      <div class="hubStats"><div><b>12,408</b>In-Game</div><div><b>48,230</b>Online</div></div>
    </header>

    <ul id="catTabs" class="catTabs" aria-label="Content types"></ul>

    <div class="hubGrid">
      <div class="hubMain">
        <div class="toolbar">
          <form class="searchBar" action="index.php" method="get" role="search">
            <input id="type" name="type" type="hidden" value="">
            <label class="sr-only" for="q">Search the hub</label>
            <input id="q" name="q" type="search" class="form-control" placeholder="Search the hub" maxlength="100">
            <button type="submit" class="btn btn-success">Search</button>
          </form>
          <div class="pageInfo"><span id="resultInfo" aria-live="polite"></span><span id="pagerTop" class="pager"></span></div>
        </div>

        <div id="hubContent"></div>

        <div class="toolbar"><div class="pageInfo"><span id="pagerBottom" class="pager"></span></div></div>
      </div>

      <aside class="hubSide">
        <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#postModal">Create a post</button>
        <div class="sideBox"><h2>Community</h2>
          <ul><li>Discussions, artwork and guides from players</li><li>News and announcements appear first</li></ul>
        </div>
        <div class="sideBox"><h2>Top contributors</h2>
          <ul><li>pixel_pete <span class="mutedText">42 posts</span></li><li>gabe_fan <span class="mutedText">31 posts</span></li><li>modder99 <span class="mutedText">27 posts</span></li></ul>
        </div>
      </aside>
    </div>
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
