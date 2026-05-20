<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['members'];

$result   = buildListQuery([
    'table'         => 'members',
    'search_fields' => ['email'],
    'order'         => 'ORDER BY email ASC',
]);

$members    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/memberlist.php';