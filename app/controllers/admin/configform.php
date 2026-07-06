<?php
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$formConfig   = $tableConfigs['config'];

$rec = getRecord('config', 'id', 1);
$link = getRecord('seo_links', 'entity_id', 1, "AND entity_type = 'organization'");
$seo  = $link ? getRecord('seo', 'id', $link->target_id) : null;
$events = getList('events', 'where is_canonical = 1 ORDER BY slug');

render('configform', [
    'rec'    => $rec,
    'config' => $formConfig,
    'seo'    => $seo,
    'images' => getList('images', 'ORDER BY id'),
    'title'  => 'Settings',
    'events' => getList('events', 'where is_canonical = 1 ORDER BY created DESC'),
]);