<?php
require __DIR__ . '/../../Helpers/functions.php';
$data = require __DIR__ . '/data.php';

$title  = 'Steam Support - FAQ';
$styles = ['/public/assets/css/support.css'];
$scripts = [];

$cat = $_GET['cat'] ?? '';
$q   = trim(substr($_GET['q'] ?? '', 0, 100));
if (!in_array($cat, $data['categories'], true)) { $cat = ''; }

$faqs = array_filter($data['faqs'], function ($f) use ($cat, $q) {
    return ($cat === '' || $f['cat'] === $cat)
        && ($q === '' || stripos($f['title'], $q) !== false);
});

function faq_count($faqs, $cat) {
    return count(array_filter($faqs, function ($f) use ($cat) { return $f['cat'] === $cat; }));
}

ob_start();
?>

<div class="support-page">

    <p class="breadcrumb">
        <a href="/app/Views/home/index.php">Home</a> &gt; Support &gt; FAQ
    </p>
    <h1 class="support-title">Frequently Asked Questions</h1>

    <div class="support-grid">

        <div class="support-main">

            <form class="support-search" method="get" role="search">
                <?php if ($cat !== ''): ?>
                    <input type="hidden" name="cat" value="<?= e($cat) ?>">
                <?php endif; ?>
                <input type="search" name="q" value="<?= e($q) ?>" maxlength="100"
                       placeholder="Search the FAQ" aria-label="Search the FAQ">
                <button type="submit">Search</button>
            </form>

            <div class="cat-tiles">
                <a class="cat-tile<?= $cat === '' ? ' active' : '' ?>" href="?">
                    <span>All</span><small><?= count($data['faqs']) ?> articles</small>
                </a>
                <?php foreach ($data['categories'] as $c): ?>
                    <a class="cat-tile<?= $cat === $c ? ' active' : '' ?>" href="?cat=<?= urlencode($c) ?>">
                        <span><?= e($c) ?></span><small><?= faq_count($data['faqs'], $c) ?> articles</small>
                    </a>
                <?php endforeach; ?>
            </div>

            <h2 class="list-heading">
                <?= $cat !== '' ? e($cat) : 'All articles' ?>
                <?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?>
            </h2>

            <?php if (!$faqs): ?>
                <p class="empty">No articles found.</p>
            <?php endif; ?>

            <?php foreach ($faqs as $id => $faq): ?>
                <a class="faq-row" href="detail.php?id=<?= (int) $id ?>">
                    <span class="faq-row-title"><?= e($faq['title']) ?></span>
                    <span class="faq-row-cat"><?= e($faq['cat']) ?></span>
                </a>
            <?php endforeach; ?>

        </div>

        <?php require __DIR__ . '/../partials/support-help.html'; ?>

    </div>
</div>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';