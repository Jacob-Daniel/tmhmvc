<?php
declare(strict_types=1);

$deleteMessage = '';
$deleteMessageType = '';
$events = null;

$item = $_GET['item']  ?? '';

if ($item > 0) {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * PER_PAGE;

    $events = geteventlistdrilldown($item, "ORDER BY id DESC", $offset, PER_PAGE);
    $totalRows = $db->query("SELECT COUNT(*) as total FROM events WHERE cat_id IN (" . rtrim(getCategories($item), ",") . ") AND active=1")->fetch_object()->total ?? 0;
    $pageinfo = [
        'records' => $totalRows,
        'page' => $page,
        'pages' => ceil($totalRows / PER_PAGE),
        'offset' => $offset
    ];

} else {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * PER_PAGE;

    $events = getList('events', "ORDER BY id DESC LIMIT $offset," . PER_PAGE);
    $pageinfo = setupPaging('events', PER_PAGE);
}

$cats = getList('categories', 'where parent_id=0 order by slug');
require __DIR__ . '/../../views/admin/eventlist.php';
