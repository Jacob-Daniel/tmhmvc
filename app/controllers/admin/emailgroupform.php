<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

$rec       = null;
$id        = null;
$groupName = '';

if ($itemId) {
    $rec = getRecord('email_groups', 'id', $itemId);

    if ($rec) {
        $id        = $rec->id;
        $groupName = $rec->group_name ?? '';
    }
}

render('emailgroupform', [
    'rec'       => $rec,
    'id'        => $id,
    'groupName' => $groupName,
]);