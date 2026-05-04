<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendContactMail(
    string $smtpHost,
    string $smtpUsername,
    string $smtpPassword,
    int $smtpPort,
    string $mailFrom,
    string $mailTo,
    string $mailSubject
): void {

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["error" => true, "message" => "Method not allowed"]);
        return;
    }

    if (empty($_POST["email"]) || empty($_POST["name"]) || empty($_POST["message"])) {
        echo json_encode(["error" => true, "message" => "All fields required"]);
        return;
    }

    if (!empty($_POST["company"])) {
        echo json_encode(["error" => true, "message" => "Invalid submission"]);
        return;
    }

    $name = trim($_POST["name"]);
    $sender_email = trim($_POST["email"]);
    $sender_message = trim($_POST["message"]);

    if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => true, "message" => "Invalid email address"]);
        return;
    }

    $message = file_get_contents(__DIR__ . "/../../resources/templates/sendmail.php");
    $message = str_replace("%name%", htmlspecialchars($name), $message);
    $message = str_replace("%message%", nl2br(htmlspecialchars($sender_message)), $message);
    $message = str_replace("%sender_email%", htmlspecialchars($sender_email), $message);

    $mail = new PHPMailer(true);
    $JSONres = ["error" => false, "message" => ""];

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

        $mail->setFrom($mailFrom);
        $mail->addAddress($mailTo);
        $mail->isHTML(true);
        $mail->Subject = $mailSubject;
        $mail->Body    = $message;

        if ($mail->send()) {
            $JSONres["message"] = "Thank you " . htmlspecialchars($name) . ", your message has been sent.";
        } else {
            $JSONres["error"] = true;
            $JSONres["message"] = $mail->ErrorInfo;
        }

    } catch (Exception $e) {
        $JSONres["error"] = true;
        $JSONres["message"] = "Message could not be sent. Please try again later.";
        error_log("Mail Error: " . $mail->ErrorInfo);
    }

    echo json_encode($JSONres);
}