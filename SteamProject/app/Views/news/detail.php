<?php
$vendorStyles = ['https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'];
$styles = [ASSET_URL . '/assets/css/news.css'];
$scripts = [
    'https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js',
    ASSET_URL . '/assets/js/news.js',
];
ob_start();
?>
<div class="container forumWrap">
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
</div>
<?php
$content = ob_get_clean();
