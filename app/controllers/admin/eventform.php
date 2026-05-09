<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page   = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

$rec = null;
if ($itemId) {
    $rec = getRecord('events', 'id', $itemId);
}

$id            = $rec?->id ?? '';
$title         = $rec?->title ?? '';
$slug          = $rec?->slug ?? '';
$summary       = $rec?->summary ?? '';
$content       = $rec?->content ?? '';
$sequence      = $rec?->sequence ?? '';
$imagepath     = $rec?->imagepath ?? '';
$iframe        = $rec?->iframe ?? '';
$metak         = $rec?->metak ?? '';
$metad         = $rec?->metad ?? '';
$price         = $rec?->price ?? '';
$start_date    = $rec?->start_date ? date('Y-m-d', $rec->start_date) : '';
$end_date      = $rec?->end_date   ? date('Y-m-d', $rec->end_date)   : '';
$start_time    = $rec?->start_time ?? '';
$end_time      = $rec?->end_time   ?? '';
$frequency     = $rec?->frequency  ?? '';
$active        = ((int)($rec?->active   ?? 0) === 1) ? 'checked' : '';
$featured      = ((int)($rec?->featured ?? 0) === 1) ? 'checked' : '';

$days_array=array('Sun','Mon','Tues','Wed','Thurs','Fri','Sat');

$categories = getList('categories', 'ORDER BY slug');

// Secondary categories
$subcats = [];
if ($rec?->id) {
    $scats = getList('event_cats', 'WHERE event_id = ' . $rec->id);
    while ($scat = $scats->fetch_object()) {
        $subcats[] = (int)$scat->cat_id;
    }
}
$categories_secondary = getList('categories', 'WHERE parent_id != 0 ORDER BY slug');

require __DIR__ . '/../../views/admin/components/imageModal.php';
require __DIR__ . '/../../views/admin/eventform.php';