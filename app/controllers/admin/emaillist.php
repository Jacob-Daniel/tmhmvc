<?php
declare(strict_types=1);

$delId = filter_input(INPUT_GET, 'delitem', FILTER_VALIDATE_INT);
$page  = filter_input(INPUT_GET, 'page',    FILTER_VALIDATE_INT) ?: 1;

$_SESSION['page'] = $page;

if ($delId) {
    $stmt = $db->prepare("DELETE FROM emails WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    $stmt->close();
}

$emails   = getList('emails', 'ORDER BY id');
$pageinfo = setupPaging('emails', 40);

render('emaillist', [
    'emails'   => $emails,
    'pageinfo' => $pageinfo,
    'page'     => $page,
]);