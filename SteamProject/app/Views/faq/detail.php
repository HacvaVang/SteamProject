<?php
require __DIR__ . '/../../Helpers/functions.php';
$data = require __DIR__ . '/data.php';

$styles = ['/public/assets/css/support.css'];
$scripts = [];

$id  = (int) ($_GET['id'] ?? 0);
$faq = $data['faqs'][$id] ?? null;

ob_start();

if (!$faq) {
    http_response_code(404);
    $title = 'Steam Support - Article not found';
    ?>
    <div class="support-page">
        <h1 class="support-title">Article not found</h1>
        <p><a href="index.php">Back to the FAQ</a></p>
    </div>
    <?php
} else {
    $title = 'Steam Support :: ' . $faq['title'];
    $related = array_filter($data['faqs'], function ($f, $k) use ($faq, $id) {
        return $f['cat'] === $faq['cat'] && $k !== $id;
    }, ARRAY_FILTER_USE_BOTH);
    ?>
    <div class="support-page">

        <p class="breadcrumb">
            <a href="/app/Views/home/index.php">Home</a> &gt;
            <a href="index.php">FAQ</a> &gt;
            <a href="index.php?cat=<?= urlencode($faq['cat']) ?>"><?= e($faq['cat']) ?></a>
        </p>

        <div class="support-grid">

            <article class="article">
                <h1 class="article-title"><?= e($faq['title']) ?></h1>
                <p class="article-meta">Last updated <?= e(format_date($faq['updated'])) ?></p>

                <?php foreach ($faq['body'] as $block): ?>
                    <?php if (isset($block['h'])): ?>
                        <h2><?= e($block['h']) ?></h2>
                    <?php elseif (isset($block['p'])): ?>
                        <p><?= e($block['p']) ?></p>
                    <?php elseif (isset($block['ul'])): ?>
                        <ul><?php foreach ($block['ul'] as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul>
                    <?php elseif (isset($block['ol'])): ?>
                        <ol><?php foreach ($block['ol'] as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ol>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="article-foot">
                    Did not find your answer?
                    <a href="/app/Views/qa/index.php">Ask the community</a>
                </div>
            </article>

            <div>
                <?php if ($related): ?>
                    <aside class="support-side">
                        <div class="side-box">
                            <h3>Related articles</h3>
                            <ul class="side-links">
                                <?php foreach ($related as $rid => $r): ?>
                                    <li><a href="detail.php?id=<?= (int) $rid ?>"><?= e($r['title']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                <?php endif; ?>
                <?php require __DIR__ . '/../partials/support-help.html'; ?>
            </div>

        </div>
    </div>
    <?php
}

$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';