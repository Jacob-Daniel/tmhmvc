<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['pages'];

$result   = buildListQuery([
    'table'         => 'pages',
    'search_fields' => ['title', 'slug'],
    'order'         => 'ORDER BY sequence ASC',
]);

$pages    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/pagelist.php';