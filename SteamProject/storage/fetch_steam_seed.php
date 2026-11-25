<?php
declare(strict_types=1);

$appIds = [
    730,     // Counter-Strike 2
    570,     // Dota 2
    1245620, // ELDEN RING
    413150,  // Stardew Valley
    1091500, // Cyberpunk 2077
    2868840, // Slay the Spire 2
    312520, // Rain World
    367520, // Hollow Knight
    1030300, // Silk Song
];

/**
 * Truy vấn IP qua DNS-over-HTTPS (Cloudflare / Google) khi DNS ISP bị chặn/lỗi.
 */
function resolveHostViaDoh(string $host): ?string
{
    $endpoints = [
        'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=A',
        'https://dns.google/resolve?name=' . urlencode($host) . '&type=A',
    ];

    foreach ($endpoints as $dohUrl) {
        $ch = curl_init($dohUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Accept: application/dns-json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close() deprecated since PHP 8.5 (no-op since 8.0) — handle is freed automatically
        unset($ch);

        if ($raw === false || $code >= 400) {
            continue;
        }

        $json = json_decode($raw, true);
        $answers = $json['Answer'] ?? [];
        if (is_array($answers)) {
            foreach ($answers as $ans) {
                $type = (int) ($ans['type'] ?? 0);
                $data = trim((string) ($ans['data'] ?? ''));
                // Type 1 = A record (IPv4)
                if ($type === 1 && filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $data;
                }
            }
        }
    }

    return null;
}

/**
 * Tải nội dung qua cURL, tự động thử DNS thường trước rồi mới dùng DoH (1.1.1.1 / 8.8.8.8).
 */
function httpGetRaw(string $url, bool $forceDoh = false): ?string
{
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 20,
                'header'  => "User-Agent: Mozilla/5.0\r\nAccept: application/json\r\n",
            ],
        ]);
        $raw = @file_get_contents($url, false, $ctx);
        return $raw === false ? null : $raw;
    }

    $parts = parse_url($url);
    $host = (string) ($parts['host'] ?? '');
    $port = (int) ($parts['port'] ?? ((($parts['scheme'] ?? 'https') === 'https') ? 443 : 80));

    $runCurl = static function (?string $pinnedIp = null) use ($url, $host, $port): array {
        $ch = curl_init($url);
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9,vi-VN,vi;q=0.8',
                'Connection: keep-alive',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if (defined('CURLOPT_DNS_SERVERS')) {
            $opts[CURLOPT_DNS_SERVERS] = '1.1.1.1,1.0.0.1,8.8.8.8';
        }

        if ($pinnedIp !== null && $host !== '') {
            $opts[CURLOPT_RESOLVE] = ["{$host}:{$port}:{$pinnedIp}"];
        }

        curl_setopt_array($ch, $opts);
        $body = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errNo = curl_errno($ch);
        $errMsg = curl_error($ch);
        // curl_close() deprecated since PHP 8.5 (no-op since 8.0) — handle is freed automatically
        unset($ch);

        return [$body, $httpCode, $errNo, $errMsg];
    };

    [$raw, $httpCode, $errNo, $errMsg] = !$forceDoh ? $runCurl(null) : [false, 0, 1, 'forced doh'];

    // Nếu lỗi DNS (CURLE_COULDNT_RESOLVE_HOST = 6) hoặc lỗi kết nối / ép DoH -> resolve qua DoH và pin IP
    if ($raw === false || $httpCode >= 400 || $errNo === 6) {
        $dohIp = $host !== '' ? resolveHostViaDoh($host) : null;
        if ($dohIp !== null) {
            [$raw, $httpCode, $errNo, $errMsg] = $runCurl($dohIp);
        }
    }

    if ($raw === false || $httpCode >= 400 || !is_string($raw) || $raw === '') {
        fwrite(STDERR, "Lỗi tải {$url} (HTTP {$httpCode}, errno {$errNo}: {$errMsg})\n");
        return null;
    }

    return $raw;
}

function httpGetJson(string $url, bool $forceDoh = true): ?array
{
    $raw = httpGetRaw($url, $forceDoh);
    if ($raw === null) {
        return null;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

function sqlQuote(string $val): string
{
    return "'" . str_replace("'", "''", $val) . "'";
}

$adminHash  = password_hash('admin123', PASSWORD_BCRYPT);
$playerHash = password_hash('player123', PASSWORD_BCRYPT);

$sqlLines = [
    'PRAGMA foreign_keys = ON;',

    '-- Seed users & wallets mẫu để gắn khóa ngoại',

    "INSERT OR IGNORE INTO users (
        account_id,
        user_name,
        email,
        password_hash,
        role,
        phone_number,
        country
    ) VALUES (
        1,
        'admin',
        'admin@steamproject.local',
        " . sqlQuote($adminHash) . ",
        'admin',
        '0900000001',
        'VN'
    );",

    "INSERT OR IGNORE INTO users (
        account_id,
        user_name,
        email,
        password_hash,
        role,
        phone_number,
        country
    ) VALUES (
        2,
        'player1',
        'player1@steamproject.local',
        " . sqlQuote($playerHash) . ",
        'user',
        '0900000002',
        'VN'
    );",

    "INSERT OR IGNORE INTO wallets (
        wallet_id,
        account_id,
        balance,
        bank_account_activated
    ) VALUES (
        1,
        1,
        5000000,
        'yes'
    );",

    "INSERT OR IGNORE INTO wallets (
        wallet_id,
        account_id,
        balance,
        bank_account_activated
    ) VALUES (
        2,
        2,
        1000000,
        'yes'
    );",

    '',
];

$imgDir = __DIR__ . '/../public/assets/images/products';
$gameIndex = 0;

foreach ($appIds as $appId) {
    // Gọi từng appid với ép DoH trước, thử cc=vn rồi fallback cc=us
    $urlVn = "https://store.steampowered.com/api/appdetails?appids={$appId}&cc=vn&l=vietnamese";
    $res = httpGetJson($urlVn, true);
    $entry = $res[(string) $appId] ?? null;

    if (!is_array($entry) || empty($entry['success']) || !is_array($entry['data'] ?? null)) {
        usleep(400000);
        $urlUs = "https://store.steampowered.com/api/appdetails?appids={$appId}&cc=us&l=english";
        $resUs = httpGetJson($urlUs, true);
        $entry = $resUs[(string) $appId] ?? null;
    }

    if (!is_array($entry) || empty($entry['success']) || !is_array($entry['data'] ?? null)) {
        fwrite(STDERR, "Bỏ qua appid {$appId} (không lấy được dữ liệu)\n");
        usleep(400000);
        continue;
    }

    $gameIndex++;
    $data = $entry['data'];
    $name = (string) ($data['name'] ?? "Game {$appId}");
    $shortDesc = trim(strip_tags((string) ($data['short_description'] ?? '')));

    $priceOverview = $data['price_overview'] ?? null;
    $isFree = !empty($data['is_free']);

    if ($isFree || !is_array($priceOverview)) {
        $basePrice = 0.0;
        $finalPrice = 0.0;
        $discountCheck = 0;
        $discountPct = 0;
    } else {
        $basePrice = ((float) ($priceOverview['initial'] ?? 0)) / 100.0;
        $finalPrice = ((float) ($priceOverview['final'] ?? $priceOverview['initial'] ?? 0)) / 100.0;
        $discountPct = (int) ($priceOverview['discount_percent'] ?? 0);
        $discountCheck = $discountPct > 0 ? 1 : 0;
    }

    $gameId = $gameIndex;
    $sqlLines[] = sprintf(
        "INSERT OR REPLACE INTO games (game_id, steam_app_id, name, description, image_url, base_price, final_price, discount_check, rating) VALUES (%d, %d, %s, %s, %s, %.2f, %.2f, %d, NULL);",
        $gameId,
        $appId,
        sqlQuote($name),
        sqlQuote($shortDesc),
        sqlQuote("steam_{$appId}.jpg"),
        $basePrice,
        $finalPrice,
        $discountCheck
    );

    // Seed game_tags từ genres
    $genres = $data['genres'] ?? [];
    $seenTags = [];
    if (is_array($genres)) {
        foreach ($genres as $g) {
            $tag = trim((string) ($g['description'] ?? ''));
            if ($tag !== '' && !isset($seenTags[$tag])) {
                $seenTags[$tag] = true;
                $sqlLines[] = sprintf(
                    "INSERT OR IGNORE INTO game_tags (game_id, tag) VALUES (%d, %s);",
                    $gameId,
                    sqlQuote($tag)
                );
            }
        }
    }

    // Seed news & game_news từ Steam News API
    // Chỉ lấy feed steam_community_announcements để tránh tin tiếng Nga/ngôn ngữ khác từ feed ngoài (PlayGround.ru, ...)
    $newsUrl = "https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/?appid={$appId}&count=2&maxlength=250&format=json&feeds=steam_community_announcements";
    $newsJson = httpGetJson($newsUrl, true);
    $newsItems = $newsJson['appnews']['newsitems'] ?? [];
    if (is_array($newsItems)) {
        foreach ($newsItems as $nIdx => $nItem) {
            $title = trim((string) ($nItem['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            // Bỏ qua title chứa ký tự Cyrillic (tiếng Nga) nếu vẫn lọt
            if (preg_match('/[\x{0400}-\x{04FF}]/u', $title)) {
                continue;
            }
            $newsId = ($gameId * 10) + ($nIdx + 1);
            $dateStr = isset($nItem['date']) ? gmdate('Y-m-d H:i:s', (int) $nItem['date']) : date('Y-m-d H:i:s');
            $pct = $discountCheck ? (float) $discountPct : 0.0;

            $sqlLines[] = sprintf(
                "INSERT OR REPLACE INTO news (news_id, name, duration, date_start, date_end) VALUES (%d, %s, 7, %s, NULL);",
                $newsId,
                sqlQuote($title),
                sqlQuote($dateStr)
            );
            $sqlLines[] = sprintf(
                "INSERT OR IGNORE INTO game_news (game_id, news_id, pct_discounted) VALUES (%d, %d, %.2f);",
                $gameId,
                $newsId,
                $pct
            );
        }
    }

    // Tải ảnh header qua cùng đường DNS/cURL
    $headerImg = (string) ($data['header_image'] ?? '');
    if ($headerImg !== '') {
        if (!is_dir($imgDir)) {
            mkdir($imgDir, 0775, true);
        }
        $imgContent = httpGetRaw($headerImg, true);
        if ($imgContent !== null) {
            file_put_contents($imgDir . "/steam_{$appId}.jpg", $imgContent);
        }
    }

    usleep(600000);
}

$outFile = __DIR__ . '/seeders.sql';
file_put_contents($outFile, implode(PHP_EOL, $sqlLines) . PHP_EOL);
echo "Đã tạo {$outFile} thành công.\n";