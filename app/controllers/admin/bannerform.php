<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['bannerform'];
$itemId       = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page         = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

$rec = $itemId ? getRecord('banners', 'id', $itemId) : null;
$pages = getList('pages','order by slug ASC');

render('bannerform', [
    'rec'    => $rec,
    'config' => $config,
    'pages' => $pages,
    'images' => getList('images', ' ORDER BY id'),
]);