<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['navigation'];

$result      = buildListQuery([
    'table'         => 'navigation',
    'search_fields' => ['label', 'slug'],
    'order'         => 'ORDER BY sequence ASC',
]);

$navigation  = $result['items'];
$pageinfo    = $result['pageinfo'];

require __DIR__ . '/../../views/admin/navlist.php';