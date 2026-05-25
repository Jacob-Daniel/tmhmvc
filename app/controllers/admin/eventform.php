<?php
declare(strict_types=1);
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['eventform'];

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page   = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

$rec = $itemId ? getRecord('events', 'id', $itemId) : null;

$link = getRecord('seo_links', 'entity_id', $itemId, "AND entity_type = 'events'");
$seo  = $link ? getRecord('seo', 'id', $link->target_id) : null;

// Unix timestamps → display strings
if ($rec?->start_date) $rec->start_date = date('Y-m-d', $rec->start_date);
if ($rec?->end_date)   $rec->end_date   = date('Y-m-d', $rec->end_date);

// Secondary category IDs for the view
$subcats = [];
if ($rec?->id) {
    $scats = getList('event_cats', 'WHERE event_id = ' . $rec->id);
    while ($scat = $scats->fetch_object()) {
        $subcats[] = (int)$scat->cat_id;
    }
}

render('eventform', [
    'rec'        => $rec,
    'config'     => $config,
    'seo'    => $seo,    
    'images'     => getList('images',     'ORDER BY id'),
    'categories' => getList('categories', 'ORDER BY slug'),
    'subcats'    => $subcats,
]);