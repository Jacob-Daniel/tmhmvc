<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['pageform'];
$itemId       = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page         = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

$rec = $itemId ? getRecord('pages', 'id', $itemId) : null;

$link = $itemId ? getRecord('seo_links', 'entity_id', $itemId, "AND entity_type = 'pages'") : null;
$seo  = $link   ? getRecord('seo', 'id', $link->target_id) : null;

render('pageform', [
    'rec'    => $rec,
    'config' => $config,
    'seo' => $seo,
    'images' => getList('images', ' ORDER BY id'),
]);