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

/**
 * Queue mass email campaign
 * 
 * @param int $emailId The email template ID
 * @param string $audience The audience type (ALLC or group ID)
 * @return array Result with success status and message
 */
function queueMassMail($db, int $emailId, string $audience): array {
    try {
        // Validate email exists
        $email = getRecord('emails', 'id', $emailId);
        if (!$email) {
            return ['success' => false, 'message' => 'Email template not found'];
        }
        
        // Build recipient query
        if ($audience === 'ALLC') {
            $recipients = getList('subscribers', 'WHERE active = 1 AND confirmed = 1');
        } else {
            $groupId = (int)$audience;
            $recipients = getList('subscribers', "WHERE group_id = $groupId AND active = 1 AND confirmed = 1");
        }
        
        if (!$recipients || $recipients->num_rows === 0) {
            return ['success' => false, 'message' => 'No recipients found for selected audience'];
        }
        
        // Check Google API daily limit
        $google_mail_limit = 500;
        $emails_sent_today = getRecordCount('mass_mail_send', "WHERE m_sent >= UNIX_TIMESTAMP(CURDATE())");
        $remaining_to_send = $google_mail_limit - $emails_sent_today;
        
        if ($remaining_to_send <= 0) {
            return ['success' => false, 'message' => 'Google Mail daily limit reached. No more emails can be sent today.'];
        }
        
        // Queue emails
        $queued = 0;
        $skipped = 0;
        
        while ($sub = $recipients->fetch_object()) {
            // Check if already queued but not sent
            $existing = getRecordCount('mass_mail_send', "WHERE email_id = $emailId AND subscriber_id = {$sub->id} AND m_sent < 1");
            
            if ($existing == 0) {
                $stmt = $db->prepare("INSERT INTO mass_mail_send (email_id, subscriber_id, m_sent, created_at) VALUES (?, ?, 0, NOW())");
                $stmt->bind_param("ii", $emailId, $sub->id);
                $stmt->execute();
                $queued++;
                $stmt->close();
            } else {
                $skipped++;
            }
        }
        
        return [
            'success' => true,
            'message' => "{$queued} emails queued for sending" . ($skipped > 0 ? " ({$skipped} already queued)" : ""),
            'queued' => $queued,
            'skipped' => $skipped
        ];
        
    } catch (Exception $e) {
        error_log("Queue mass mail error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}


/**
 * Queue mass email campaign
 * 
 * @param mysqli $db Database connection
 * @param int $emailId The email template ID
 * @param string $audience The audience type (ALLC or group ID)
 * @return array Result with success status and message
 */
function queueMassMail($db, int $emailId, string $audience): array {
    try {
        // Validate email exists
        $email = getRecord('emails', 'id', $emailId);
        if (!$email) {
            return ['success' => false, 'message' => 'Email template not found'];
        }
        
        if ($email->active != 1) {
            return ['success' => false, 'message' => 'Email template is not active'];
        }
        
        // Build recipient query
        if ($audience === 'ALLC') {
            // Get all confirmed subscribers
            $recipients = getList('members', 'WHERE email_conf = 1 AND active = 1');
        } else {
            $groupId = (int)$audience;
            // Get subscribers in specific group (adjust table/field names as needed)
            $recipients = getList('members', "WHERE group_id = $groupId AND email_conf = 1 AND active = 1");
        }
        
        if (!$recipients || $recipients->num_rows === 0) {
            return ['success' => false, 'message' => 'No active/confirmed recipients found for selected audience'];
        }
        
        // Check Google API daily limit
        $google_mail_limit = 500;
        $emails_sent_today = getRecordCount('mass_mail_send', "WHERE m_sent >= UNIX_TIMESTAMP(CURDATE()) AND m_sent > 0");
        $remaining_to_send = $google_mail_limit - $emails_sent_today;
        
        if ($remaining_to_send <= 0 && $remaining_to_send < $recipients->num_rows) {
            return [
                'success' => false, 
                'message' => "Google Mail daily limit reached. Only {$remaining_to_send} emails can be sent today, but you have {$recipients->num_rows} recipients."
            ];
        }
        
        // Queue emails
        $queued = 0;
        $skipped = 0;
        
        while ($sub = $recipients->fetch_object()) {
            // Check if already queued but not sent
            $existing = getRecordCount('mass_mail_send', "WHERE email_id = $emailId AND member_id = {$sub->id} AND m_sent < 1");
            
            if ($existing == 0) {
                $stmt = $db->prepare("INSERT INTO mass_mail_send (email_id, member_id, m_sent, error, created_at) VALUES (?, ?, 0, '', NOW())");
                $stmt->bind_param("ii", $emailId, $sub->id);
                $stmt->execute();
                $queued++;
                $stmt->close();
            } else {
                $skipped++;
            }
        }
        
        return [
            'success' => true,
            'message' => "{$queued} emails queued for sending" . ($skipped > 0 ? " ({$skipped} already queued)" : ""),
            'queued' => $queued,
            'skipped' => $skipped
        ];
        
    } catch (Exception $e) {
        error_log("Queue mass mail error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

/**
 * Process a single queued email (called by cron job or manually)
 * 
 * @param mysqli $db Database connection
 * @param int $queueId The mass_mail_send record ID
 * @return bool Success status
 */
function processQueuedEmail($db, int $queueId): bool {
    try {
        // Get queue record
        $queue = getRecord('mass_mail_send', 'id', $queueId);
        if (!$queue || $queue->m_sent > 0) {
            return false;
        }
        
        // Get email template
        $email = getRecord('emails', 'id', $queue->email_id);
        if (!$email || $email->active != 1) {
            $errorMsg = 'Email template not found or inactive';
            markQueueAsFailed($db, $queueId, $errorMsg);
            return false;
        }
        
        // Get member/subscriber
        $member = getRecord('members', 'id', $queue->member_id);
        if (!$member || $member->active != 1 || $member->email_conf != 1) {
            $errorMsg = 'Member not found, inactive, or not confirmed';
            markQueueAsFailed($db, $queueId, $errorMsg);
            return false;
        }
        
        // Prepare subject and body with replacements
        $subject = $email->em_subject ?? $email->em_name;
        $body = $email->em_body;
        
        // Replace user-specific placeholders
        $body = str_replace('{FNAME}', $member->first_name ?? '', $body);
        $body = str_replace('{LNAME}', $member->last_name ?? '', $body);
        $body = str_replace('{EMAIL}', $member->email, $body);
        
        // Get Google token
        $token = getGoogleAccessToken($db);
        if (!$token) {
            $errorMsg = 'Google access token not available';
            markQueueAsFailed($db, $queueId, $errorMsg);
            return false;
        }
        
        // Send using your existing sendGsuite function
        $from = "info@{$_SERVER['HTTP_HOST']}";
        $mailtype = 'm'; // mass mail type
        
        // Note: sendGsuite returns true on success, but you may want to modify it to return status
        $sent = sendGsuite($email->id, null, $member->id, $subject, $from, $mailtype, $token);
        
        // Update queue status
        $sentTime = $sent ? time() : 0;
        $errorMsg = $sent ? '' : 'Failed to send via Google API';
        
        $stmt = $db->prepare("UPDATE mass_mail_send SET m_sent = ?, error = ? WHERE id = ?");
        $stmt->bind_param("isi", $sentTime, $errorMsg, $queueId);
        $stmt->execute();
        $stmt->close();
        
        return $sent;
        
    } catch (Exception $e) {
        error_log("Process queued email error: " . $e->getMessage());
        markQueueAsFailed($db, $queueId, $e->getMessage());
        return false;
    }
}

/**
 * Mark a queued email as failed
 */
function markQueueAsFailed($db, int $queueId, string $errorMsg) {
    $stmt = $db->prepare("UPDATE mass_mail_send SET error = ?, m_sent = -1 WHERE id = ?");
    $stmt->bind_param("si", $errorMsg, $queueId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Get Google access token from database
 */
function getGoogleAccessToken($db) {
    $token = getRecord('oauth_tokens', 'id', 1);
    if (!$token) {
        return null;
    }
    
    // Check if token needs refresh
    if (time() >= intval($token->expires_at) - 300) {
        $refreshed = refreshGoogleAccessToken($db, 1);
        if ($refreshed) {
            $token = getRecord('oauth_tokens', 'id', 1);
        } else {
            return null;
        }
    }
    
    return $token->access_token;
}

/**
 * Process multiple queued emails (for cron job)
 * 
 * @param mysqli $db Database connection
 * @param int $limit Maximum emails to process in this run
 * @return array Processing results
 */
function processEmailQueue($db, int $limit = 50): array {
    $results = [
        'processed' => 0,
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    // Get pending emails, order by oldest first
    $pending = $db->query("
        SELECT id FROM mass_mail_send 
        WHERE m_sent = 0 AND (error = '' OR error IS NULL) 
        ORDER BY created_at ASC 
        LIMIT $limit
    ");
    
    if (!$pending) {
        return $results;
    }
    
    while ($queue = $pending->fetch_object()) {
        $results['processed']++;
        if (processQueuedEmail($db, $queue->id)) {
            $results['success']++;
        } else {
            $results['failed']++;
            // Get error message
            $failed = getRecord('mass_mail_send', 'id', $queue->id);
            if ($failed && $failed->error) {
                $results['errors'][] = "Queue ID {$queue->id}: {$failed->error}";
            }
        }
        
        // Small delay to avoid rate limits
        usleep(500000); // 0.5 second delay between emails
    }
    
    return $results;
}

/**
 * Get queue statistics
 */
function getQueueStats($db): array {
    $total = getRecordCount('mass_mail_send', 'WHERE 1=1');
    $pending = getRecordCount('mass_mail_send', 'WHERE m_sent = 0 AND (error = "" OR error IS NULL)');
    $sent = getRecordCount('mass_mail_send', 'WHERE m_sent > 0');
    $failed = getRecordCount('mass_mail_send', 'WHERE m_sent = -1 OR error != ""');
    $sent_today = getRecordCount('mass_mail_send', 'WHERE m_sent >= UNIX_TIMESTAMP(CURDATE()) AND m_sent > 0');
    
    return [
        'total' => $total,
        'pending' => $pending,
        'sent' => $sent,
        'failed' => $failed,
        'sent_today' => $sent_today
    ];
}