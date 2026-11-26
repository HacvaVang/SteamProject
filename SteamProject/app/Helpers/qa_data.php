<?php
/* Q&A storage. Uses app/Data/qa.json, seeded from app/Views/qa/data.php on first load.
   Replace with Question / Answer models (MySQL) later. */
require_once __DIR__ . '/functions.php';
function getQaFile(): string
{
    return dirname(__DIR__) . '/Data/qa.json';
}

function saveQaData(array $data): bool
{
    $file = getQaFile();
    $dir  = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $json !== false && file_put_contents($file, $json . PHP_EOL, LOCK_EX) !== false;
}

function getQaData(): array
{
    $file = getQaFile();
    if (is_file($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    $seed = require __DIR__ . '/../Views/qa/data.php';
    foreach ($seed as $qid => $q) {
        foreach ($q['answers'] as $i => $a) {
            $seed[$qid]['answers'][$i]['id'] = $i + 1;   // answers need an id so admin can target them
        }
    }
    saveQaData($seed);
    return $seed;
}

/* Approve / Reject / Delete buttons for one item ($aid = 0 means the question itself). */
function qa_actions($status, $qid, $aid, $tab, $from = 'list')
{
    $buttons = [];
    if ($status !== 'approved') { $buttons[] = ['approve', 'Approve', 'btn-green']; }
    if ($status !== 'rejected') { $buttons[] = ['reject', $status === 'approved' ? 'Unpublish' : 'Reject', 'btn-blue']; }
    $buttons[] = ['delete', 'Delete', 'btn-red'];

    $warn = (int) $aid === 0
        ? 'Delete this question and all its answers permanently?'
        : 'Delete this answer permanently?';

    foreach ($buttons as $b) {
        $confirm = $b[0] === 'delete' ? ' onsubmit="return confirm(\'' . $warn . '\');"' : '';
        echo '<form method="POST" action="' . e(BASE_URL) . '/admin/qa" class="inline-form"' . $confirm . '>'
           . '<input type="hidden" name="action" value="' . $b[0] . '">'
           . '<input type="hidden" name="qid" value="' . (int) $qid . '">'
           . '<input type="hidden" name="aid" value="' . (int) $aid . '">'
           . '<input type="hidden" name="tab" value="' . e($tab) . '">'
           . '<input type="hidden" name="from" value="' . e($from) . '">'
           . '<button type="submit" class="' . $b[2] . '">' . $b[1] . '</button>'
           . '</form>';
    }
}