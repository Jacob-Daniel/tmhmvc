<?php
header('Cache-Control: no-store, no-cache, must-revalidate');
$fld   = $_GET['fld']   ?? 'slug';
$val   = isset($_GET['val']) ? urldecode($_GET['val']) : '';
$table = $_GET['table'] ?? 'events';
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * PER_PAGE;

if (!isset($tableConfigs[$table])) die("Invalid table");

$config = $tableConfigs[$table];

if (!in_array($fld, $config['fields'], true)) die("Invalid field");

$reserved = ['table', 'fld', 'val', '_', 'page'];
$whereFields = [];
foreach ($_GET as $key => $value) {
    if (in_array($key, $reserved, true)) continue;
    if ($value === '') continue;
    if (!in_array($key, $config['fields'], true)) continue; 
    $whereFields[$key] = $value;
}
$result = buildListQuery([
    'table'         => $table,
    'search_fields' => [$fld],
    'order'         => 'ORDER BY id DESC',
    'where_fields'  => $whereFields,   // dynamic, driven by URL params    
]);

error_log("getlist returning: " . $result['items']->num_rows . " rows");

echo buildTable($result['items'], $config);