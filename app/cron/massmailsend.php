<?php
/**
 * Mass Mail Send — Cron Job
 * Run via:  php /path/to/app/cron/massmailsend.php
 * Suggested crontab:  * * * * * php /var/www/app/cron/massmailsend.php >> /var/log/massmail.log 2>&1
 */
declare(strict_types=1);

// Refuse to run over HTTP
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

require_once dirname(__DIR__) . '/bootstrap/app.php';
require_once dirname(__DIR__) . '/shared/functions.php';
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

ini_set('log_errors',  '1');
ini_set('error_log', dirname(__DIR__, 2) . '/storage/logs/massmailsend.log');

// ── Config ───────────────────────────────────────────────────────────────────
const MAX_EMAILS_PER_DAY = 500;
const TOKEN_BUFFER        = 3600; // refresh if < 60 min remaining

// ── Daily send count ─────────────────────────────────────────────────────────
$result    = $db->query(
    "SELECT COUNT(*) AS sent_today
     FROM mass_mail_send
     WHERE m_sent BETWEEN UNIX_TIMESTAMP(CURDATE())
                      AND UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY) - 1"
);
$sentToday = (int) ($result->fetch_assoc()['sent_today'] ?? 0);

if ($sentToday >= MAX_EMAILS_PER_DAY) {
    error_log("Daily limit reached ($sentToday/" . MAX_EMAILS_PER_DAY . "). Exiting.");
    exit;
}

// ── Google client ─────────────────────────────────────────────────────────────
$client = new Google_Client();
$client->setClientId(GOOGLEAPI_CLIENT_ID);
$client->setClientSecret(GOOGLEAPI_CLIENT_SECRET);
$client->setAccessType('offline');
$client->setScopes(Google_Service_Gmail::GMAIL_SEND);

// ── Load stored tokens ────────────────────────────────────────────────────────
$result    = $db->query("SELECT access_token, refresh_token, expires_at FROM oauth_tokens WHERE id = 1");
$tokenData = $result->fetch_assoc();

if (!$tokenData) {
    error_log("No token data found. Exiting.");
    exit;
}

$accessToken  = $tokenData['access_token'];
$refreshToken = $tokenData['refresh_token'];
$expiresAt    = (int) $tokenData['expires_at'];
$currentTime  = time();

$client->setAccessToken([
    'access_token'  => $accessToken,
    'refresh_token' => $refreshToken,
    'expires_in'    => $expiresAt - $currentTime,
    'created'       => $currentTime - ($expiresAt - $currentTime),
]);

// ── Peek at the queue before deciding to refresh ──────────────────────────────
$pending = getList('mass_mail_send', 'WHERE m_sent = 0');

if ($pending && $pending->num_rows > 0) {
    if ($currentTime > ($expiresAt - TOKEN_BUFFER)) {
        if (empty($refreshToken)) {
            error_log("Refresh token missing. Exiting.");
            exit;
        }

        error_log("Refreshing access token...");
        $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if (isset($newToken['error'])) {
            error_log("Token refresh failed: " . ($newToken['error_description'] ?? $newToken['error']));
            exit;
        }

        if (!isset($newToken['access_token'])) {
            error_log("Token refresh returned no access_token: " . print_r($newToken, true));
            exit;
        }

        $accessToken  = $newToken['access_token'];
        $newExpiresAt = $currentTime + (int) $newToken['expires_in'];
        $rfExpiry     = $currentTime + (5 * 86400);

        $stmt = $db->prepare(
            "UPDATE oauth_tokens SET access_token = ?, expires_at = ?, refresh_token_expires_at = ? WHERE id = 1"
        );
        $stmt->bind_param('sii', $accessToken, $newExpiresAt, $rfExpiry);

        if (!$stmt->execute()) {
            error_log("Failed to persist refreshed token: " . $db->error);
            exit;
        }

        error_log("Token refreshed. New expiry: " . date('Y-m-d H:i:s', $newExpiresAt));
    }
}

// ── Fetch pending batch (respecting daily cap) ────────────────────────────────
$remaining = MAX_EMAILS_PER_DAY - $sentToday;
$mlist     = getList('mass_mail_send', "WHERE m_sent = 0 LIMIT {$remaining}");

if (!$mlist || !$mlist->num_rows) {
    error_log("No pending emails.");
    exit;
}

// ── Send ──────────────────────────────────────────────────────────────────────
while ($m = $mlist->fetch_object()) {
    $member = getRecord('members', 'id', $m->member_id);

    if (!$member || !$member->id) {
        error_log("Member {$m->member_id} not found — skipping row {$m->id}.");
        continue;
    }

    $sent = sendGsuite(
        $m->email_id,
        $m->list_id,
        $m->member_id,
        $m->m_subj,
        $m->m_from,
        'm',
        $accessToken
    );

    if ($sent) {
        $ts  = time();
        $sql = "UPDATE mass_mail_send SET m_sent = {$ts} WHERE id = {$m->id}";
        if (!$db->query($sql)) {
            error_log("Could not mark row {$m->id} as sent: " . $db->error);
        }
    } else {
        error_log("sendGsuite() failed for member {$m->member_id} (row {$m->id}).");
    }
}

error_log("Cron run complete.");
exit;