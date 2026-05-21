<?php
declare(strict_types=1);

$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['subscribers'];

$groups = getList('email_groups', ' ORDER BY group_name');

$result = buildListQuery([
    'table'         => 'subscribers',
    'search_fields' => ['email', 'fname', 'lname'],
    'order'         => 'ORDER BY lname ASC',
    'where_fields'  => [
        'group_id' => null, 
        'unsub'    => 0,
    ],
]);
$subscribers    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/subscriberlist.php';