<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

// Available email templates and groups to drive the selects
$emailTemplates = getList('emails',       'WHERE active = 1 ORDER BY em_name');
$groups         = getList('email_groups', 'ORDER BY group_name');

// Load an existing send record if reviewing/re-sending
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
        $subject = $rec->m_subj  ?? '';
        $from    = $rec->m_from  ?? '';
        $emailId = $rec->email_id ?? null;
        $listId  = $rec->list_id  ?? null;
    }
}

render('massmailform', [
    'rec'            => $rec,
    'id'             => $id,
    'subject'        => $subject,
    'from'           => $from,
    'emailId'        => $emailId,
    'listId'         => $listId,
    'emailTemplates' => $emailTemplates,
    'groups'         => $groups,
]);