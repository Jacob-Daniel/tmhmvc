<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$delRaw = filter_input(INPUT_GET, 'del');

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$_SESSION['page'] = $page;

// Delete images if requested
if ($itemId && $delRaw) {
    $delIds = array_filter(array_map('intval', explode(',', $delRaw)));
    foreach ($delIds as $imageId) {
        $stmt = $db->prepare("DELETE FROM item_images WHERE page_id=? AND images_id=?");
        $stmt->bind_param('ii', $itemId, $imageId);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch page / items
$pages = getList('pages', ' ORDER BY id');
$images = getList('images', ' ORDER BY id');

$rec = null;
$id = null;
$active = '';
$title = '';
$slug = '';
$content = '';
$images = getList('images', ' ORDER BY id');
$pages  = getList('pages', ' ORDER BY id');

if ($itemId) {
    $rec = getRecord('pages', 'id', $itemId);
    if ($rec) {
        $id      = $rec->id;
        $slug    = $rec->slug ?? '';
        $title   = $rec->title ?? '';
        $content = $rec->content ?? '';
        $active = ((int)$rec->active === 1); // boolean
        $item_images = getList('item_images', ' WHERE page_id=' . (int)$rec->id);
    }
}

// Pass everything
render('pageform', [
    'rec'     => $rec,
    'id'      => $id,
    'active'  => $active,
    'slug'    => $slug,
    'title'   => $title,
    'content' => $content,
    'pages'   => $pages,
    'images'  => $images,
]);