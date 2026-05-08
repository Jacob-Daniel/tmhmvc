<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$listId   = filter_input(INPUT_POST, 'list_id',  FILTER_VALIDATE_INT);
$emailId  = filter_input(INPUT_POST, 'email_id', FILTER_VALIDATE_INT);
$subject  = trim($_POST['m_subj']   ?? '');
$from     = trim($_POST['m_from']   ?? '');
$campaign = trim($_POST['campaign'] ?? '');

// ── Validate ──────────────────────────────────────────────────────────────────
if (!$listId) {
    echo json_encode(['success' => false, 'error' => 'Please select a recipient group.']);
    exit;
}
if (!$emailId) {
    echo json_encode(['success' => false, 'error' => 'Please select an email template.']);
    exit;
}
if ($subject === '') {
    echo json_encode(['success' => false, 'error' => 'Subject line is required.']);
    exit;
}
if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'A valid From address is required.']);
    exit;
}

// ── Email template ────────────────────────────────────────────────────────────
$emailRec = getRow('emails', ['id', 'em_name'], 'WHERE id = ?', [$emailId]);

if (!$emailRec) {
    echo json_encode(['success' => false, 'error' => 'Email template not found.']);
    exit;
}

$emName = $emailRec['em_name'];

// ── Members in group ──────────────────────────────────────────────────────────
$members = getListWhere(
    'members',
    "WHERE group_id = ? AND unsub = 0 AND email <> ''",
    'i',
    [$listId]
);

if (!$members || $members->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'No active members found in that group.']);
    exit;
}

// ── Insert one queued row per member ──────────────────────────────────────────
$stmt = $db->prepare(
    "INSERT INTO mass_mail_send
        (member_id, list_id, email_id, m_subj, m_from, em_name, campaign, m_sent)
     VALUES
        (?, ?, ?, ?, ?, ?, ?, 0)"
);

if (!$stmt) {
    error_log('savemassmail prepare failed: ' . $db->error);
    echo json_encode(['success' => false, 'error' => 'Database error — could not prepare statement.']);
    exit;
}

$insertedCount = 0;
$skippedCount  = 0;

while ($member = $members->fetch_object()) {

    // Skip if already queued and unsent for this member + template
    $already = getListWhere(
        'mass_mail_send',
        'WHERE email_id = ? AND member_id = ? AND m_sent = 0',
        'ii',
        [$emailId, $member->id]
    );

    if ($already && $already->num_rows > 0) {
        $skippedCount++;
        continue;
    }

    $stmt->bind_param(
        'iiissss',
        $member->id,
        $listId,
        $emailId,
        $subject,
        $from,
        $emName,
        $campaign
    );

    if ($stmt->execute()) {
        $insertedCount++;
    } else {
        error_log("savemassmail insert failed for member {$member->id}: " . $stmt->error);
    }
}

$stmt->close();

// ── Response ──────────────────────────────────────────────────────────────────
$message = "{$insertedCount} email(s) queued — sending will begin within 5 minutes.";
if ($skippedCount > 0) {
    $message .= " {$skippedCount} already queued, skipped.";
}

echo json_encode([
    'success' => true,
    'message' => $message,
    'queued'  => $insertedCount,
    'skipped' => $skippedCount,
]);
exit;