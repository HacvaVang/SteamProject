<?php
$title = 'Steam - Home';

ob_start();
?>

<h1>Welcome to Steam</h1>

<p>This is the homepage.</p>

<section>
    <h2>Featured Games</h2>
</section>

<?php
$content = ob_get_clean();

require __DIR__ . './../layouts/main.php';
?>