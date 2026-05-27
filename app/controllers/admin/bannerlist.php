<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['banners'];

$result      = buildListQuery([
    'table'         => 'banners',
    'search_fields' => ['label', 'slug'],
    'order'         => 'ORDER BY sequence ASC',
]);

$banners  = $result['items'];
$pageinfo    = $result['pageinfo'];

require __DIR__ . '/../../views/admin/bannerlist.php';