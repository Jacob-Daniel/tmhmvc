<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Send password reset email to admin
 *
 * @param string $smtpHost
 * @param string $smtpUsername
 * @param string $smtpPassword
 * @param int $smtpPort
 * @param string $mailFrom
 * @param string $mailTo
 * @param string $token
 */
function sendResetEmail(
    string $smtpHost,
    string $smtpUsername,
    string $smtpPassword,
    int $smtpPort,
    string $mailFrom,
    string $mailTo,
    string $token
): void {

    $message = file_get_contents(__DIR__ . "/../../resources/templates/resetPassword.php");
    $resetLink = BASE_URL . "/admin/updateAdminPassword.php?token=" . urlencode($token);

    // replace placeholder in template
    $message = str_replace("%url%", $resetLink, $message);

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUsername;
        $mail->Password   = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtpPort;

        $mail->SMTPOptions = [
            "ssl" => [
                "verify_peer"       => false,
                "verify_peer_name"  => false,
                "allow_self_signed" => true,
            ],
        ];

        $mail->setFrom($mailFrom, "Admin Reset");
        $mail->addAddress($mailTo);
        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request";
        $mail->Body    = $message;

        $mail->send();

    } catch (Exception $e) {
        error_log("Password reset mail error: " . $mail->ErrorInfo);
    }
}