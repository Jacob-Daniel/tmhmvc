<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['bannerform'];
$itemId       = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page         = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

$rec = $itemId ? getRecord('banners', 'id', $itemId) : null;

render('pageform', [
    'rec'    => $rec,
    'config' => $config,
    'images' => getList('images', ' ORDER BY id'),
]);