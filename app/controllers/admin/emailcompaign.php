<?php
declare(strict_types=1);

require_once(__DIR__ . '/../../shared/functions.php');

session_start();

// Get parameters
$condition = filter_input(INPUT_GET, 'condition', FILTER_SANITIZE_STRING);
$refresh = filter_input(INPUT_GET, 'refresh', FILTER_VALIDATE_BOOLEAN);

// Handle token refresh
if ($condition === 'refresh' || $refresh) {
    $refreshed = refreshGoogleAccessToken($db, 1);
    $_SESSION['refresh_message'] = '<div class="alert alert-success">Token refreshed successfully!</div>';
    header('Location: /admin/emailcampaign');
    exit;
}

// Get data
$google_mail_limit = 500;
$total_pending = getRecordCount('mass_mail_send', 'WHERE m_sent < 1');
$emails_sent_today = getRecordCount('mass_mail_send', "WHERE m_sent >= UNIX_TIMESTAMP(CURDATE())");
$remaining_to_send = $google_mail_limit - $emails_sent_today;

$check = getRecord('oauth_tokens', 'id', 1);
$ems = getList('emails', 'ORDER BY id DESC');
$grs = getList('email_groups', 'ORDER BY group_name');
$emstosend = getList('emails', 'WHERE id IN (SELECT email_id FROM mass_mail_send WHERE m_sent < 1)');
$authUrl = getGoogleOAuthUrl();

// Get refresh message if exists
$refresh_message = $_SESSION['refresh_message'] ?? null;
unset($_SESSION['refresh_message']);

// Render view
render('emailcampaign', [
    'remaining_to_send' => $remaining_to_send,
    'total_pending' => $total_pending,
    'emails_to_send' => $emstosend,
    'check' => $check,
    'authUrl' => $authUrl,
    'ems' => $ems,
    'grs' => $grs,
    'refresh_message' => $refresh_message
]);