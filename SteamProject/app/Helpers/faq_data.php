<?php
/* FAQ storage. Uses app/Data/faq.json, seeded from app/Views/faq/data.php on first load.
   Replace with an FAQ model (MySQL) later. */
require_once __DIR__ . '/functions.php';

function getFaqFile(): string
{
    return dirname(__DIR__) . '/Data/faq.json';
}

function saveFaqData(array $data): bool
{
    $file = getFaqFile();
    $dir  = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $json !== false && file_put_contents($file, $json . PHP_EOL, LOCK_EX) !== false;
}

function getFaqData(): array
{
    $file = getFaqFile();
    if (is_file($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded) && isset($decoded['faqs'], $decoded['categories'])) {
            return $decoded;
        }
    }

    $seed = require __DIR__ . '/../Views/faq/data.php';
    saveFaqData($seed);
    return $seed;
}

/* Body blocks -> editable text.
   "# Heading", "- bullet", "1. step", blank line between blocks, anything else is a paragraph. */
function faqBodyToText(array $blocks): string
{
    $out = [];
    foreach ($blocks as $b) {
        if (isset($b['h'])) {
            $out[] = '# ' . $b['h'];
        } elseif (isset($b['p'])) {
            $out[] = $b['p'];
        } elseif (isset($b['ul'])) {
            $out[] = implode("\n", array_map(function ($i) { return '- ' . $i; }, $b['ul']));
        } elseif (isset($b['ol'])) {
            $lines = [];
            foreach (array_values($b['ol']) as $n => $i) { $lines[] = ($n + 1) . '. ' . $i; }
            $out[] = implode("\n", $lines);
        }
    }
    return implode("\n\n", $out);
}

/* Editable text -> body blocks. */
function faqTextToBody(string $text): array
{
    $blocks = [];
    $type   = null;   // 'p', 'ul' or 'ol'
    $buf    = [];

    $flush = function () use (&$blocks, &$type, &$buf) {
        if ($type === 'p') {
            $blocks[] = ['p' => implode(' ', $buf)];
        } elseif ($type !== null) {
            $blocks[] = [$type => $buf];
        }
        $type = null;
        $buf  = [];
    };

    foreach (explode("\n", str_replace("\r", '', $text)) as $line) {
        $line = trim($line);
        if ($line === '') { $flush(); continue; }

        if (preg_match('/^#\s+(.+)$/', $line, $m)) {
            $flush();
            $blocks[] = ['h' => $m[1]];
            continue;
        }

        if (preg_match('/^-\s+(.+)$/', $line, $m))          { $kind = 'ul'; }
        elseif (preg_match('/^\d+\.\s+(.+)$/', $line, $m))  { $kind = 'ol'; }
        else                                                { $kind = 'p'; $m = [1 => $line]; }

        if ($type !== $kind) { $flush(); $type = $kind; }
        $buf[] = $m[1];
    }
    $flush();

    return $blocks;
}