<?php
declare(strict_types=1);

// item    = member id (0 = new)
// condition = group_id (always required)
$itemId  = filter_input(INPUT_GET, 'item',      FILTER_VALIDATE_INT);
$groupId = filter_input(INPUT_GET, 'condition', FILTER_VALIDATE_INT);

$rec   = null;
$id    = null;
$email = '';
$fname = '';
$lname = '';

$group = $groupId ? getRecord('email_groups', 'id', $groupId) : null;

if ($itemId) {
    $rec = getRecord('email_group_members', 'id', $itemId);

    if ($rec) {
        $id      = $rec->id;
        $groupId = $rec->group_id; // trust the DB over the param if editing
        $email   = $rec->email ?? '';
        $fname   = $rec->fname ?? '';
        $lname   = $rec->lname ?? '';
        $group   = getRecord('email_groups', 'id', $groupId);
    }
}

render('emailgroupmemberform', [
    'rec'     => $rec,
    'id'      => $id,
    'groupId' => $groupId,
    'group'   => $group,
    'email'   => $email,
    'fname'   => $fname,
    'lname'   => $lname,
]);