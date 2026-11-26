<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';   // isAdminUser()
require_once __DIR__ . '/../../../Helpers/functions.php';
require_once __DIR__ . '/../../../Helpers/qa_data.php';

$styles  = ['/public/assets/css/support.css'];
$scripts = [];

$data        = getQaData();
$sessionUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin     = isAdminUser($sessionUser);

$statuses = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];
$tab = $_GET['status'] ?? 'pending';
if (!isset($statuses[$tab])) { $tab = 'pending'; }

/* ---- Handle a moderation action, then redirect back (POST/redirect/GET) ---- */
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $qid    = (int) ($_POST['qid'] ?? 0);
    $aid    = (int) ($_POST['aid'] ?? 0);          // 0 = the action targets the question itself
    $back   = (string) ($_POST['tab'] ?? 'pending');
    if (!isset($statuses[$back])) { $back = 'pending'; }

    $newStatus = ['approve' => 'approved', 'reject' => 'rejected'][$action] ?? null;
    $done = false;

    if (isset($data[$qid]) && ($newStatus !== null || $action === 'delete')) {
        if ($aid === 0) {
            if ($action === 'delete') { unset($data[$qid]); }   // also removes its answers
            else { $data[$qid]['status'] = $newStatus; }
            $done = true;
        } else {
            foreach ($data[$qid]['answers'] as $i => $a) {
                if ((int) ($a['id'] ?? 0) !== $aid) { continue; }
                if ($action === 'delete') { unset($data[$qid]['answers'][$i]); }
                else { $data[$qid]['answers'][$i]['status'] = $newStatus; }
                $data[$qid]['answers'] = array_values($data[$qid]['answers']);
                $done = true;
                break;
            }
        }
    }

    $msg = ($done && saveQaData($data)) ? ($action === 'delete' ? 'deleted' : $newStatus) : 'error';
    /* Return to the detail page if the action came from there (unless the question was deleted). */
    if (($_POST['from'] ?? '') === 'detail' && isset($data[$qid])) {
        $url = BASE_URL . '/admin/qa/detail?id=' . $qid . '&msg=' . $msg;
    } else {
        $url = BASE_URL . '/admin/qa?status=' . $back . '&msg=' . $msg;
    }
    header('Location: ' . $url);
    exit;
}

$notices = [
    'approved' => 'Item approved and published.',
    'rejected' => 'Item rejected.',
    'deleted'  => 'Item deleted.',
];
$msg = (string) ($_GET['msg'] ?? '');

/* ---- Build the lists for the current tab ---- */
$questions = array_filter($data, function ($q) use ($tab) { return $q['status'] === $tab; });

$answers = [];
foreach ($data as $qid => $q) {
    foreach ($q['answers'] as $a) {
        if ($a['status'] === $tab) {
            $answers[] = ['qid' => $qid, 'question' => $q, 'answer' => $a];
        }
    }
}

function qa_count($data, $status) {
    $n = 0;
    foreach ($data as $q) {
        if ($q['status'] === $status) { $n++; }
        foreach ($q['answers'] as $a) {
            if ($a['status'] === $status) { $n++; }
        }
    }
    return $n;
}

?>
<section class="support-page admin-qa" aria-label="Q&amp;A moderation">

    <p class="breadcrumb">Admin &gt; Community Q&amp;A</p>
    <h1 class="support-title">Q&amp;A Moderation</h1>

<?php if (!$isAdmin): ?>

    <div class="side-box">
        <h3>Access restricted</h3>
        <p>This page is only available to admin accounts. Use the mode switch at the bottom of the page to test as admin.</p>
        <a class="btn-blue" href="/login">Sign in as admin</a>
    </div>

<?php else: ?>

    <?php if (isset($notices[$msg])): ?>
        <p class="notice" role="status"><?= e($notices[$msg]) ?></p>
    <?php elseif ($msg === 'error'): ?>
        <p class="alert-error" role="alert">The action could not be completed.</p>
    <?php endif; ?>

    <nav class="sort-tabs" aria-label="Filter by status">
        <?php foreach ($statuses as $key => $label): ?>
            <a class="<?= $tab === $key ? 'active' : '' ?>" href="?status=<?= $key ?>">
                <?= e($label) ?> (<?= qa_count($data, $key) ?>)
            </a>
        <?php endforeach; ?>
    </nav>

    <h2 class="list-heading">Questions (<?= count($questions) ?>)</h2>

    <?php if (!$questions): ?>
        <p class="empty">No <?= e($tab) ?> questions.</p>
    <?php endif; ?>

    <?php foreach ($questions as $qid => $q): ?>
        <div class="mod-item">
            <div class="mod-head">
                <a class="mod-title" href="<?= e(BASE_URL) ?>/admin/qa/detail?id=<?= (int) $qid ?>"><?= e($q['title']) ?></a>
                <span class="q-meta"><?= e($q['author']) ?> &middot; <?= e(format_date($q['date'])) ?></span>
            </div>
            <p class="mod-body"><?= nl2br(e($q['body'])) ?></p>
            <?php foreach ($q['tags'] as $t): ?><span class="tag"><?= e($t) ?></span><?php endforeach; ?>
            <div class="mod-actions"><?php qa_actions($q['status'], $qid, 0, $tab); ?></div>
        </div>
    <?php endforeach; ?>

    <h2 class="list-heading">Answers (<?= count($answers) ?>)</h2>

    <?php if (!$answers): ?>
        <p class="empty">No <?= e($tab) ?> answers.</p>
    <?php endif; ?>

    <?php foreach ($answers as $row): $a = $row['answer']; ?>
        <div class="mod-item">
            <p class="mod-ctx">On: <a href="<?= e(BASE_URL) ?>/admin/qa/detail?id=<?= (int) $row['qid'] ?>"><?= e($row['question']['title']) ?></a></p>
            <div class="mod-head">
                <span class="mod-title"><?= e($a['author']) ?></span>
                <span class="q-meta"><?= e(format_date($a['date'])) ?></span>
            </div>
            <p class="mod-body"><?= nl2br(e($a['body'])) ?></p>
            <div class="mod-actions"><?php qa_actions($a['status'], $row['qid'], $a['id'] ?? 0, $tab); ?></div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</section>