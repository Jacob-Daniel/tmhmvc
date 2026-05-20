<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['email_groups'];

$result   = buildListQuery([
    'table'         => 'email_groups',
    'search_fields' => ['group_name'],
    'order'         => 'ORDER BY group_name ASC',
]);

$emailgroups    = $result['items'];
$pageinfo = $result['pageinfo'];

require __DIR__ . '/../../views/admin/emailgrouplist.php';