<?php
require __DIR__ . '/../../Helpers/functions.php';
require __DIR__ . '/../../Helpers/qa_data.php';
$all = getQaData();

$styles = ['/public/assets/css/support.css'];
$scripts = [];

$id = (int) ($_GET['id'] ?? 0);
$q  = $all[$id] ?? null;

/* Pending or rejected questions are treated as not found. */
if ($q && $q['status'] !== 'approved') { $q = null; }

ob_start();

if (!$q) {
    http_response_code(404);
    $title = 'Steam Support - Question not found';
    ?>
    <div class="support-page">
        <h1 class="support-title">Question not found</h1>
        <p><a href="index.php">Back to Community Q&amp;A</a></p>
    </div>
    <?php
} else {
    $title = 'Steam Support :: ' . $q['title'];

    $answers = array_filter($q['answers'], function ($a) { return $a['status'] === 'approved'; });
    usort($answers, function ($a, $b) {
        return [$b['accepted'], $b['votes']] <=> [$a['accepted'], $a['votes']];
    });
    ?>
    <div class="support-page">

        <p class="breadcrumb">
            <a href="/app/Views/home/index.php">Home</a> &gt;
            <a href="index.php">Community Q&amp;A</a>
        </p>

        <?php if (isset($_GET['submitted'])): ?>
            <p class="notice">Thanks! Your answer was submitted and will appear after an admin approves it.</p>
        <?php endif; ?>

        <div class="support-grid">

            <div class="support-main">

                <h1 class="article-title"><?= e($q['title']) ?></h1>
                <p class="article-meta">
                    Asked <?= e(format_date($q['date'])) ?> by <?= e($q['author']) ?> &middot; <?= (int) $q['views'] ?> views
                </p>

                <div class="post">
                    <div class="post-votes"><b><?= (int) $q['votes'] ?></b><small>votes</small></div>
                    <div class="post-body">
                        <p><?= nl2br(e($q['body'])) ?></p>
                        <?php foreach ($q['tags'] as $t): ?>
                            <a class="tag" href="index.php?tag=<?= urlencode($t) ?>"><?= e($t) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <h2 class="list-heading"><?= count($answers) ?> answer<?= count($answers) === 1 ? '' : 's' ?></h2>

                <?php foreach ($answers as $a): ?>
                    <div class="post<?= $a['accepted'] ? ' accepted' : '' ?>">
                        <div class="post-votes">
                            <b><?= (int) $a['votes'] ?></b><small>votes</small>
                            <?php if ($a['accepted']): ?><span class="check" title="Accepted answer">&#10003;</span><?php endif; ?>
                        </div>
                        <div class="post-body">
                            <p><?= nl2br(e($a['body'])) ?></p>
                            <p class="q-meta"><?= e($a['author']) ?> answered <?= e(format_date($a['date'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$answers): ?>
                    <p class="empty">No answers yet. Be the first to help.</p>
                <?php endif; ?>

                <h2 class="list-heading">Your answer</h2>
                <!-- TODO: point to the real route once it exists. The server should save it with status 'pending'. -->
                <form action="/qa/answer" method="post" class="qa-form">
                    <input type="hidden" name="question_id" value="<?= $id ?>">
                    <label for="aBody">Answer</label>
                    <textarea id="aBody" name="body" rows="6" required minlength="20" maxlength="2000"
                              placeholder="Write your answer (20-2000 characters)"></textarea>
                    <p class="form-note">Answers are reviewed by an admin before they are published.</p>
                    <button type="submit" class="btn-green">Post answer</button>
                </form>

            </div>

            <?php require __DIR__ . '/../partials/support-help.html'; ?>

        </div>
    </div>
    <?php
}

$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';