<?php
declare(strict_types=1);

$delId = filter_input(INPUT_GET, 'delitem', FILTER_VALIDATE_INT);
$page  = filter_input(INPUT_GET, 'page',    FILTER_VALIDATE_INT) ?: 1;

$_SESSION['page'] = $page;

if ($delId) {
    $stmt = $db->prepare("DELETE FROM email_groups WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    $stmt->close();
}

$groups   = getList('email_groups', 'ORDER BY id');
$pageinfo = setupPaging('email_groups', 40);

render('emailgrouplist', [
    'groups'   => $groups,
    'pageinfo' => $pageinfo,
    'page'     => $page,
]);