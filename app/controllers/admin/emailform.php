<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);

$rec    = null;
$id     = null;
$title  = 'New Email';
$active = false;

$events = getList('events', 'WHERE active = 1');
$pages  = getList('pages',  'WHERE active = 1');

if ($itemId) {
    $rec = getRecord('emails', 'id', $itemId);

    if ($rec) {
        $id     = $rec->id;
        $title  = $rec->em_name ?: 'Edit Email';
        $active = ((int)$rec->active === 1);
    }
}

render('emailform', [
    'rec'    => $rec,
    'id'     => $id,
    'title'  => $title,
    'active' => $active,
    'events' => $events,
    'pages'  => $pages,
]);