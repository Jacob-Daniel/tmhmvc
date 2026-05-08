<?php
declare(strict_types=1);

// -------------------------------------------------------
// Optional: allow a manual token refresh from the UI
// -------------------------------------------------------
if (
    filter_input(INPUT_GET, 'condition') === 'refresh'
    && function_exists('refreshGoogleAccessToken')
) {
    refreshGoogleAccessToken($db, 1);
}

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

// -------------------------------------------------------
// Email templates + recipient groups (drive the <select>s)
// -------------------------------------------------------
$emailTemplates = getList('emails',       'WHERE active = 1 ORDER BY em_name');
$groups         = getList('email_groups', 'ORDER BY group_name');

// -------------------------------------------------------
// Load existing send record if editing / re-sending
// -------------------------------------------------------
$rec     = null;
$id      = null;
$subject = '';
$from    = '';
$emailId = null;
$listId  = null;

if ($itemId) {
    $rec = getRecord('mass_mail_send', 'id', $itemId);
    if ($rec) {
        $id      = $rec->id;
        $subject = $rec->m_subj   ?? '';
        $from    = $rec->m_from   ?? '';
        $emailId = $rec->email_id ?? null;
        $listId  = $rec->list_id  ?? null;
    }
}

// -------------------------------------------------------
// Google OAuth / token state
// -------------------------------------------------------
$GOOGLE_MAIL_LIMIT  = 500;
$TOKEN_WARN_SECONDS = 300; // warn when < 5 min to expiry

$emailsSentToday  = getRecordCount(
    'mass_mail_send',
    "WHERE m_sent >= UNIX_TIMESTAMP(CURDATE())"
);
$totalPending     = getRecordCount('mass_mail_send', 'WHERE m_sent < 1');
$remainingToSend  = $GOOGLE_MAIL_LIMIT - $emailsSentToday;

// Templates that still have pending sends (for the info panel)
$pendingTemplates = getList(
    'emails',
    'WHERE id IN (SELECT email_id FROM mass_mail_send WHERE m_sent < 1)'
);

$oauthToken   = getRecord('oauth_tokens', 'id', 1);
$authUrl      = function_exists('getGoogleOAuthUrl') ? getGoogleOAuthUrl() : '#';

// Possible states: 'valid' | 'expiring' | 'missing'
$tokenStatus  = 'missing';
$tokenExpires = null;

if ($oauthToken && $oauthToken->id) {
    $tokenExpires = (int) $oauthToken->expires_at;
    if (time() >= $tokenExpires - $TOKEN_WARN_SECONDS) {
        $tokenStatus = 'expiring';
    } else {
        $tokenStatus = 'valid';
    }
}

// -------------------------------------------------------
// Render
// -------------------------------------------------------
render('massmailform', [
    // Form record
    'rec'              => $rec,
    'id'               => $id,
    'subject'          => $subject,
    'from'             => $from,
    'emailId'          => $emailId,
    'listId'           => $listId,
    // Selects
    'emailTemplates'   => $emailTemplates,
    'groups'           => $groups,
    // Quota info
    'remainingToSend'  => $remainingToSend,
    'totalPending'     => $totalPending,
    'pendingTemplates' => $pendingTemplates,
    // Google token state
    'tokenStatus'      => $tokenStatus,   // 'valid' | 'expiring' | 'missing'
    'tokenExpires'     => $tokenExpires,  // Unix timestamp or null
    'authUrl'          => $authUrl,
]);