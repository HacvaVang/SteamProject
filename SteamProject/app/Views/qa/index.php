<?php
require __DIR__ . '/../../Helpers/functions.php';
$all = require __DIR__ . '/data.php';

$title  = 'Steam Support - Community Q&A';
$styles = ['/public/assets/css/support.css'];

/* Only approved questions are public. */
$questions = array_filter($all, function ($q) { return $q['status'] === 'approved'; });

function approved_answers($q) {
    return array_filter($q['answers'], function ($a) { return $a['status'] === 'approved'; });
}

$search = trim(substr($_GET['q'] ?? '', 0, 100));
$tag    = trim(substr($_GET['tag'] ?? '', 0, 30));
$sort   = $_GET['sort'] ?? 'newest';
if (!in_array($sort, ['newest', 'votes', 'unanswered'], true)) { $sort = 'newest'; }

$questions = array_filter($questions, function ($q) use ($search, $tag, $sort) {
    return ($search === '' || stripos($q['title'] . ' ' . $q['body'], $search) !== false)
        && ($tag === '' || in_array($tag, $q['tags'], true))
        && ($sort !== 'unanswered' || count(approved_answers($q)) === 0);
});

uasort($questions, function ($a, $b) use ($sort) {
    return $sort === 'votes' ? $b['votes'] <=> $a['votes'] : strcmp($b['date'], $a['date']);
});

ob_start();
?>

<div class="support-page">

    <p class="breadcrumb">
        <a href="/app/Views/home/index.php">Home</a> &gt; Support &gt; Community Q&amp;A
    </p>
    <h1 class="support-title">Community Q&amp;A</h1>

    <?php if (isset($_GET['submitted'])): ?>
        <p class="notice">Thanks! Your question was submitted and will appear after an admin approves it.</p>
    <?php endif; ?>

    <div class="support-grid">

        <div class="support-main">

            <!-- Ask form (opens inline, no JavaScript) -->
            <details class="ask-box" <?= isset($_GET['ask']) ? 'open' : '' ?>>
                <summary class="btn-green">Ask a Question</summary>
                <!-- TODO: point to the real route once it exists. The server should save it with status 'pending'. -->
                <form action="/qa/ask" method="post" class="qa-form">
                    <label for="qTitle">Title</label>
                    <input id="qTitle" name="title" type="text" required minlength="10" maxlength="150"
                           placeholder="Be specific (10-150 characters)">

                    <label for="qBody">Details</label>
                    <textarea id="qBody" name="body" rows="6" required minlength="20" maxlength="2000"
                              placeholder="Describe your problem (20-2000 characters)"></textarea>

                    <label for="qTags">Tags (optional, comma separated)</label>
                    <input id="qTags" name="tags" type="text" maxlength="80" placeholder="crash, windows">

                    <p class="form-note">Questions are reviewed by an admin before they are published.</p>
                    <button type="submit" class="btn-green">Submit question</button>
                </form>
            </details>

            <div class="qa-toolbar">
                <form class="support-search" method="get" role="search">
                    <input type="hidden" name="sort" value="<?= e($sort) ?>">
                    <input type="search" name="q" value="<?= e($search) ?>" maxlength="100"
                           placeholder="Search questions" aria-label="Search questions">
                    <button type="submit">Search</button>
                </form>
                <nav class="sort-tabs" aria-label="Sort questions">
                    <?php foreach (['newest' => 'Newest', 'votes' => 'Top voted', 'unanswered' => 'Unanswered'] as $key => $label): ?>
                        <a class="<?= $sort === $key ? 'active' : '' ?>"
                           href="?sort=<?= $key ?><?= $search !== '' ? '&q=' . urlencode($search) : '' ?><?= $tag !== '' ? '&tag=' . urlencode($tag) : '' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="list-heading">
                <?= count($questions) ?> question<?= count($questions) === 1 ? '' : 's' ?>
                <?= $tag !== '' ? ' tagged [' . e($tag) . '] <a href="?">(clear)</a>' : '' ?>
            </div>

            <?php if (!$questions): ?>
                <p class="empty">No questions found.</p>
            <?php endif; ?>

            <?php foreach ($questions as $id => $q):
                $answers = approved_answers($q);
                $solved  = in_array(true, array_column($answers, 'accepted'), true);
            ?>
                <div class="q-row">
                    <div class="q-stats">
                        <span><b><?= (int) $q['votes'] ?></b> votes</span>
                        <span class="<?= $solved ? 'solved' : (count($answers) ? 'has-answers' : '') ?>">
                            <b><?= count($answers) ?></b> answers
                        </span>
                        <span><?= (int) $q['views'] ?> views</span>
                    </div>
                    <div class="q-main">
                        <a class="q-title" href="detail.php?id=<?= (int) $id ?>"><?= e($q['title']) ?></a>
                        <p class="q-excerpt"><?= e(mb_strimwidth($q['body'], 0, 160, '...')) ?></p>
                        <div class="q-foot">
                            <span>
                                <?php foreach ($q['tags'] as $t): ?>
                                    <a class="tag" href="?tag=<?= urlencode($t) ?>"><?= e($t) ?></a>
                                <?php endforeach; ?>
                            </span>
                            <span class="q-meta"><?= e($q['author']) ?> asked <?= e(format_date($q['date'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <?php require __DIR__ . '/../partials/support-help.html'; ?>

    </div>
</div>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';