<?php
declare(strict_types=1);

$tableConfigs = require APP_PATH . '/shared/table_configs.php'; 
$config = $tableConfigs['events'];

if ((int)($_GET['item'] ?? 0) > 0) {

    $page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * PER_PAGE;
    $item   = (int)$_GET['item'];
    $events = getListDrillDown($item,'events','cat_id', 'ORDER BY id DESC', $offset, PER_PAGE);
    $catIds = rtrim(getCategories($item), ',');
    $total  = $db->query("SELECT COUNT(*) as total FROM events WHERE cat_id IN ($catIds) AND active=1")->fetch_object()->total ?? 0;
    $pageinfo = ['records' => $total, 'page' => $page, 'pages' => ceil($total / PER_PAGE), 'offset' => $offset];
    $search = '';

} else {

    $result = buildListQuery([
        'table'         => 'events',
        'search_fields' => ['title', 'description', 'location'],
        'order'         => 'ORDER BY id DESC',
        'extra_where'   => 'AND active=1',
        'drill_id'      => (int)($_GET['item'] ?? 0),
        'drill_field'   => 'cat_id',
    ]);

    $events   = $result['items'];
    $pageinfo = $result['pageinfo'];
    $search   = $result['search'];
}

$cats = getList('categories', 'WHERE parent_id=0 ORDER BY slug');

require __DIR__ . '/../../views/admin/eventlist.php';
