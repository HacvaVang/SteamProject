<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';   // isAdminUser()
require_once __DIR__ . '/../../../Helpers/functions.php';
require_once __DIR__ . '/../../../Helpers/qa_data.php';

$styles  = ['/public/assets/css/support.css'];
$scripts = [];

$data        = getQaData();
$sessionUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin     = isAdminUser($sessionUser);

$id = (int) ($_GET['id'] ?? 0);
$q  = $data[$id] ?? null;

$notices = [
    'approved' => 'Item approved and published.',
    'rejected' => 'Item rejected.',
    'deleted'  => 'Item deleted.',
];
$msg = (string) ($_GET['msg'] ?? '');

if ($isAdmin && !$q) { http_response_code(404); }
?>
<section class="support-page admin-qa" aria-label="Q&amp;A moderation detail">

    <p class="breadcrumb">
        Admin &gt; <a href="<?= e(BASE_URL) ?>/admin/qa">Community Q&amp;A</a> &gt; Question #<?= $id ?>
    </p>

<?php if (!$isAdmin): ?>

    <div class="side-box">
        <h3>Access restricted</h3>
        <p>This page is only available to admin accounts. Use the mode switch at the bottom of the page to test as admin.</p>
        <a class="btn-blue" href="/login">Sign in as admin</a>
    </div>

<?php elseif (!$q): ?>

    <h1 class="support-title">Question not found</h1>
    <p class="empty">It may have been deleted.</p>
    <p><a class="btn-blue" href="<?= e(BASE_URL) ?>/admin/qa">Back to moderation</a></p>

<?php else: ?>

    <?php if (isset($notices[$msg])): ?>
        <p class="notice" role="status"><?= e($notices[$msg]) ?></p>
    <?php elseif ($msg === 'error'): ?>
        <p class="alert-error" role="alert">The action could not be completed.</p>
    <?php endif; ?>

    <p class="mod-links">
        <a href="<?= e(BASE_URL) ?>/admin/qa?status=<?= e($q['status']) ?>">&larr; Back to <?= e($q['status']) ?> list</a>
        <?php if ($q['status'] === 'approved'): ?>
            &middot; <a href="/app/Views/qa/detail.php?id=<?= $id ?>">View public page</a>
        <?php endif; ?>
    </p>

    <!-- Question -->
    <div class="mod-item">
        <div class="mod-head">
            <h1 class="mod-question"><?= e($q['title']) ?>
                <span class="status-badge status-<?= e($q['status']) ?>"><?= e($q['status']) ?></span>
            </h1>
        </div>
        <p class="q-meta">
            <?= e($q['author']) ?> &middot; <?= e(format_date($q['date'])) ?>
            &middot; <?= (int) $q['views'] ?> views &middot; <?= (int) $q['votes'] ?> votes
        </p>
        <p class="mod-body"><?= nl2br(e($q['body'])) ?></p>
        <?php foreach ($q['tags'] as $t): ?><span class="tag"><?= e($t) ?></span><?php endforeach; ?>
        <div class="mod-actions"><?php qa_actions($q['status'], $id, 0, $q['status'], 'detail'); ?></div>
    </div>

    <!-- Answers (all statuses) -->
    <h2 class="list-heading">Answers (<?= count($q['answers']) ?>)</h2>

    <?php if (!$q['answers']): ?>
        <p class="empty">This question has no answers.</p>
    <?php endif; ?>

    <?php foreach ($q['answers'] as $a): ?>
        <div class="mod-item">
            <div class="mod-head">
                <span class="mod-title"><?= e($a['author']) ?>
                    <span class="status-badge status-<?= e($a['status']) ?>"><?= e($a['status']) ?></span>
                    <?php if (!empty($a['accepted'])): ?><span class="status-badge status-approved">accepted answer</span><?php endif; ?>
                </span>
                <span class="q-meta"><?= e(format_date($a['date'])) ?> &middot; <?= (int) $a['votes'] ?> votes</span>
            </div>
            <p class="mod-body"><?= nl2br(e($a['body'])) ?></p>
            <div class="mod-actions"><?php qa_actions($a['status'], $id, $a['id'] ?? 0, $q['status'], 'detail'); ?></div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</section>