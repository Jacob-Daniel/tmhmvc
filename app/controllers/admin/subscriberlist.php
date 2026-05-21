<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['subscribers'];

$result   = buildListQuery([
    'table'         => 'subscribers',
    'search_fields' => ['email'],
    'order'         => 'ORDER BY email ASC',
]);

$subscribers    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/subscriberlist.php';