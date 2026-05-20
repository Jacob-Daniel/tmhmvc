<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['emails'];

$result   = buildListQuery([
    'table'         => 'emails',
    'search_fields' => ['em_name', 'em_body'],
    'order'         => 'ORDER BY em_name ASC',
]);

$emails    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/emaillist.php';