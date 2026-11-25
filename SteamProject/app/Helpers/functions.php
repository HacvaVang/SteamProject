<?php
if (!function_exists('e')) {
    function e($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
}

if (!function_exists('format_date')) {
    function format_date($date) { return date('j M, Y', strtotime($date)); }
}