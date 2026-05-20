<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['categories'];

$result   = buildListQuery([
    'table'         => 'categories',
    'search_fields' => ['title', 'slug'],
    'order'         => 'ORDER BY sequence ASC',
]);

$categories    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/catlist.php';