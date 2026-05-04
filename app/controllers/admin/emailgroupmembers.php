<?php
declare(strict_types=1);

$groupId = filter_input(INPUT_GET, 'item',    FILTER_VALIDATE_INT);
$delId   = filter_input(INPUT_GET, 'delitem', FILTER_VALIDATE_INT);
$page    = filter_input(INPUT_GET, 'page',    FILTER_VALIDATE_INT) ?: 1;

$_SESSION['page'] = $page;

if (!$groupId) {
    render('emailgroupmembers', ['group' => null, 'members' => null, 'groupId' => null, 'page' => $page]);
    exit;
}

// Delete a single member from this group
if ($delId) {
    $stmt = $db->prepare("DELETE FROM email_group_members WHERE id = ? AND group_id = ?");
    $stmt->bind_param('ii', $delId, $groupId);
    $stmt->execute();
    $stmt->close();
}

$group   = getRecord('email_groups', 'id', $groupId);
$members = getList('members', "WHERE group_id = {$groupId} ORDER BY lname, fname");

render('emailgroupmembers', [
    'group'   => $group,
    'members' => $members,
    'groupId' => $groupId,
    'page'    => $page,
]);