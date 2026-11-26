<?php
require_once __DIR__ . '/../../../Helpers/product_data.php';   // isAdminUser()
require_once __DIR__ . '/../../../Helpers/functions.php';
require_once __DIR__ . '/../../../Helpers/faq_data.php';

$styles  = ['/public/assets/css/support.css'];
$scripts = [];

$data        = getFaqData();
$categories  = $data['categories'];
$sessionUser = $currentUser ?? ($_SESSION['user'] ?? null);
$isAdmin     = isAdminUser($sessionUser);

$error = '';
$form  = ['id' => 0, 'title' => '', 'cat' => $categories[0], 'body' => ''];

/* ---- Handle add / edit / delete ---- */
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save') {
        $form = [
            'id'    => (int) ($_POST['id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'cat'   => (string) ($_POST['cat'] ?? ''),
            'body'  => trim((string) ($_POST['body'] ?? '')),
        ];
        $titleLen = mb_strlen($form['title']);
        $bodyLen  = mb_strlen($form['body']);
        $isEdit   = $form['id'] > 0;

        if ($titleLen < 5 || $titleLen > 150) {
            $error = 'Title must be 5-150 characters.';
        } elseif (!in_array($form['cat'], $categories, true)) {
            $error = 'Choose a valid category.';
        } elseif ($bodyLen < 10 || $bodyLen > 5000) {
            $error = 'Content must be 5000 characters or fewer, and at least 10.';
        } elseif ($isEdit && !isset($data['faqs'][$form['id']])) {
            $error = 'The article you are editing no longer exists.';
        } else {
            $blocks = faqTextToBody($form['body']);

            if (!$blocks) {
                $error = 'Content cannot be empty.';
            } else {
                if (!$isEdit) {
                    $form['id'] = $data['faqs'] ? max(array_keys($data['faqs'])) + 1 : 1;
                }
                $data['faqs'][$form['id']] = [
                    'title'   => $form['title'],
                    'cat'     => $form['cat'],
                    'updated' => date('Y-m-d'),
                    'body'    => $blocks,
                ];
                ksort($data['faqs']);

                $msg = saveFaqData($data) ? ($isEdit ? 'saved' : 'created') : 'error';
                header('Location: ' . BASE_URL . '/admin/faqs?msg=' . $msg);
                exit;
            }
        }
    } elseif ($action === 'delete') {
        $delId = (int) ($_POST['id'] ?? 0);
        $msg   = 'error';
        if (isset($data['faqs'][$delId])) {
            unset($data['faqs'][$delId]);
            $msg = saveFaqData($data) ? 'deleted' : 'error';
        }
        header('Location: ' . BASE_URL . '/admin/faqs?msg=' . $msg);
        exit;
    }
}

/* ---- Load an article into the form when editing ---- */
$editId = (int) ($_GET['edit'] ?? 0);
if ($isAdmin && $error === '' && $editId > 0 && isset($data['faqs'][$editId])) {
    $f    = $data['faqs'][$editId];
    $form = ['id' => $editId, 'title' => $f['title'], 'cat' => $f['cat'], 'body' => faqBodyToText($f['body'])];
}

$notices = [
    'created' => 'Article created.',
    'saved'   => 'Changes saved.',
    'deleted' => 'Article deleted.',
];
$msg = (string) ($_GET['msg'] ?? '');
?>
<section class="support-page admin-faq" aria-label="FAQ management">

    <p class="breadcrumb">Admin &gt; FAQ</p>
    <h1 class="support-title">FAQ Management</h1>

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

    <?php if ($error !== ''): ?>
        <p class="alert-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <!-- Add / edit form -->
    <form method="POST" action="<?= e(BASE_URL) ?>/admin/faqs" class="qa-form" id="faqForm">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= (int) $form['id'] ?>">

        <h2 class="form-heading"><?= $form['id'] ? 'Edit article #' . (int) $form['id'] : 'Add a new article' ?></h2>

        <label for="faqTitle">Title</label>
        <input id="faqTitle" name="title" type="text" required minlength="5" maxlength="150"
               value="<?= e($form['title']) ?>" placeholder="5-150 characters">

        <label for="faqCat">Category</label>
        <select id="faqCat" name="cat" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?= e($c) ?>"<?= $form['cat'] === $c ? ' selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="faqBody">Content</label>
        <textarea id="faqBody" name="body" rows="14" required minlength="10" maxlength="5000"><?= e($form['body']) ?></textarea>
        <p class="form-note">
            Separate blocks with a blank line. Start a line with <code># </code> for a heading,
            <code>- </code> for a bullet, or <code>1. </code> for a numbered step. Other text is a paragraph.
        </p>

        <div class="mod-actions">
            <button type="submit" class="btn-green"><?= $form['id'] ? 'Save changes' : 'Add article' ?></button>
            <?php if ($form['id']): ?>
                <a class="btn-blue" href="<?= e(BASE_URL) ?>/admin/faqs">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Article list -->
    <h2 class="list-heading">Articles (<?= count($data['faqs']) ?>)</h2>

    <?php if (!$data['faqs']): ?>
        <p class="empty">No articles yet.</p>
    <?php endif; ?>

    <?php foreach ($data['faqs'] as $id => $faq): ?>
        <div class="faq-admin-row">
            <div class="faq-admin-info">
                <span class="faq-row-title">#<?= (int) $id ?> &mdash; <?= e($faq['title']) ?></span>
                <span class="q-meta"><?= e($faq['cat']) ?> &middot; updated <?= e(format_date($faq['updated'])) ?></span>
            </div>
            <div class="mod-actions">
                <a class="btn-blue" href="/faq/detail?id=<?= (int) $id ?>">View</a>
                <a class="btn-blue" href="<?= e(BASE_URL) ?>/admin/faqs?edit=<?= (int) $id ?>#faqForm">Edit</a>
                <form method="POST" action="<?= e(BASE_URL) ?>/admin/faqs" class="inline-form"
                      onsubmit="return confirm('Delete this article permanently?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">
                    <button type="submit" class="btn-red">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</section>