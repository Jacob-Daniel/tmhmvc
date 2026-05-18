<?php
header('Cache-Control: no-store, no-cache, must-revalidate');
$fld   = $_GET['fld']   ?? 'slug';
$val   = isset($_GET['val']) ? urldecode($_GET['val']) : '';
$table = $_GET['table'] ?? 'products';
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * PER_PAGE;

if (!isset($tableConfigs[$table])) die("Invalid table");

$config = $tableConfigs[$table];

if (!in_array($fld, $config['fields'], true)) die("Invalid field");

$result = buildListQuery([
    'table'         => $table,
    'search_fields' => [$fld],
    'order'         => 'ORDER BY id DESC',
]);

error_log("getlist returning: " . $result['items']->num_rows . " rows");

echo buildTable($result['items'], $config);