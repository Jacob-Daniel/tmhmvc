<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/shared/sendresetmail.php';

// -----------------------------------------------------------------------------
// CONFIG SWITCH
// -----------------------------------------------------------------------------
if (!defined('MAIL_CONFIG_SOURCE')) {
    define('MAIL_CONFIG_SOURCE', 'env'); // default to constants if not set
}

// -----------------------------------------------------------------------------
// SELECT MAIL CONFIG
// -----------------------------------------------------------------------------
if (MAIL_CONFIG_SOURCE === 'db') {
    $config = getRecord('config', 'id', 1);
    if (!$config) {
        http_response_code(500);
        echo json_encode(["error" => true, "message" => "Mail config missing"]);
        exit;
    }

    $emailHost     = $config->email_host;
    $emailUser     = $config->email_username;
    $emailPassword = $config->email_password;
    $emailPort     = (int)$config->email_port;
    $emailFrom     = $config->site_email;
    $emailTo       = $config->site_email;
    $emailSubject  = 'Mail Form Message';
} else {
    $emailHost     = MAIL_SMTPHOST ?? '';
    $emailUser     = MAIL_USERNAME ?? '';
    $emailPassword = MAIL_PASSWORD ?? '';
    $emailPort     = MAIL_PORT ?? 587;
    $emailFrom     = MAIL_FROM_ADDRESS ?? '';
    $emailTo       = MAIL_TO_ADDRESS ?? '';
    $emailSubject  = MAIL_SUBJECT ?? 'Mail Form Message';
}


// -----------------------------------------------------------------------------
// VALIDATE POST
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => true, "message" => "Method not allowed"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => true, "message" => "Invalid email address"]);
    exit;
}

// -----------------------------------------------------------------------------
// LOOKUP ADMIN AND CREATE RESET TOKEN
// -----------------------------------------------------------------------------
global $db;
$emailEsc = $db->real_escape_string($email);
$sql = "SELECT * FROM adminusers WHERE email='$emailEsc' LIMIT 1";
$res = $db->query($sql);

if ($res && $res->num_rows > 0) {
    $admin = $res->fetch_assoc();

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $_POST = [
        'admin_user_id' => $admin['id'],
        'token_hash'    => $tokenHash,
        'expires_at'    => $expires
    ];
    insertRecord('admin_password_resets');

    sendResetEmail(
        $emailHost,
        $emailUser,
        $emailPassword,
        $emailPort,
        $emailFrom,
        $admin['email'],
        $token
    );
}

// Always return generic message (do not reveal if email exists)
echo json_encode([
    "error" => false,
    "message" => "If that email exists, a reset link has been sent."
]);
exit;