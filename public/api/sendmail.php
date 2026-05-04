<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/shared/sendmail.php';

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
// SEND MAIL
// -----------------------------------------------------------------------------
$result = sendContactMail(
    $emailHost,
    $emailUser,
    $emailPassword,
    $emailPort,
    $emailFrom,
    $emailTo,
    $emailSubject
);

exit;