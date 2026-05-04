<?php
if (isset($_GET['page']) && $_GET['page'] == -1) {
    unset($_SESSION['srch_fld'], $_SESSION['srch_val'], $_SESSION['page_item']);
}
$item = $_GET['item']  ?? '';
$page = (isset($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$_SESSION['page'] = $page;
$offset = ($page * 20) - 20;

if (isset($item) && $item > 0) {
    $records = getpagelistdrilldown((int)$item, 'ORDER BY title');
    $_SESSION['page_item'] = (int)$item;
} else {
    $records = getList('pages', "ORDER BY id LIMIT {$offset},20");
}

if (!empty($_SESSION['srch_val'])) {
    $val = $db->real_escape_string($_SESSION['srch_val']);
    $prods = $db->query("SELECT * FROM pages WHERE title LIKE '%{$val}%'");
}

$pageinfo = setupPaging('pages', '20');
$parents = getList('pages');
render('pagelist', [
    'records'       => $records ?? [],
    'pageinfo'      => $pageinfo ?? ['pages' => 1, 'page' => 1],
    'item'          => $item ?? 0,
    'deleteMessage' => $deleteMessage ?? null,
    'parents'       => $parents ?? [],
]);
