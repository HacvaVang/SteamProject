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
      <ul id="catTabs" class="detailTabs" aria-label="Content types"></ul>
    </div>

    <div id="hubContent"></div>
</div>

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
<?php
$content = ob_get_clean();
