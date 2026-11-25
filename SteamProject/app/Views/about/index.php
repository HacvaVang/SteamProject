<?php
$title  = 'Steam - About';
$styles  = ['/public/assets/css/nav.css', '/public/assets/css/about.css'];
$scripts = [];

if (!function_exists('e')) {
    function e($text) { return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'); }
}
$stats = [
    ['value' => '1,000+', 'label' => 'Games'],
    ['value' => '50,000+', 'label' => 'Players'],
    ['value' => '24/7', 'label' => 'Community'],
];

$features = [
    ['title' => 'Discover',  'text' => 'Browse a growing catalog of games with detailed pages, screenshots and system requirements.'],
    ['title' => 'Play',      'text' => 'Buy a game, add it to your library and jump in with a few clicks.'],
    ['title' => 'Connect',   'text' => 'Join community hubs to share guides, reviews, artwork and discussions with other players.'],
];

$team = [
    ['name' => 'Member One',   'role' => 'Project Lead / Backend'],
    ['name' => 'Member Two',   'role' => 'Frontend'],
    ['name' => 'Member Three', 'role' => 'Database'],
    ['name' => 'Member Four',  'role' => 'Community / News'],
];

ob_start();
require __DIR__ . '/../layouts/nav.html';
?>

<div class="about-page">

    <section class="about-hero">
        <h1>About Steam</h1>
        <p>
            This project is a Steam-inspired platform for discovering, buying and
            discussing games, built as a web programming course project.
        </p>
    </section>

    <section class="about-stats">
        <?php foreach ($stats as $stat): ?>
            <div class="stat">
                <span class="stat-value"><?= e($stat['value']) ?></span>
                <span class="stat-label"><?= e($stat['label']) ?></span>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="about-section">
        <h2>Our Mission</h2>
        <p>
            We want to make finding and enjoying games simple. Everything on the
            site is designed around three ideas: discover something new, play it
            right away, and share the experience with others.
        </p>
    </section>

    <section class="about-section">
        <h2>What You Can Do</h2>
        <div class="feature-grid">
            <?php foreach ($features as $feature): ?>
                <div class="feature">
                    <h3><?= e($feature['title']) ?></h3>
                    <p><?= e($feature['text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-section">
        <h2>The Team</h2>
        <div class="team-grid">
            <?php foreach ($team as $member): ?>
                <div class="member">
                    <div class="member-avatar"><?= e(strtoupper(substr($member['name'], 0, 1))) ?></div>
                    <div class="member-name"><?= e($member['name']) ?></div>
                    <div class="member-role"><?= e($member['role']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="about-cta">
        <p>Have a question or feedback?</p>
        <a class="btn-contact" href="/app/Views/contact/index.php">Contact Us</a>
    </section>

</div>

<?php
$content = ob_get_clean();

require __DIR__ . '/../layouts/main.php';
