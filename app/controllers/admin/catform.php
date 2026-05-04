<?php
declare(strict_types=1);

$delId = filter_input(INPUT_GET, 'del', FILTER_VALIDATE_INT);

if ($delId && $delId > 0) {
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException($db->error);
    }
    if (!$stmt->bind_param('i', $delId)) {
        throw new RuntimeException($stmt->error);
    }
    if (!$stmt->execute()) {
        throw new RuntimeException($stmt->error);
    }
    $stmt->close();
}


$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$page   = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

$_SESSION['page'] = $page;

$images = getList('images', 'ORDER BY id');
$pcats  = getList('categories', 'WHERE parent_id = 0');

/*
|--------------------------------------------------------------------------
| Defaults (Create Mode)
|--------------------------------------------------------------------------
*/

$id        = '';
$parent    = 0;
$title     = '';
$slug      = '';
$content   = '';
$imagepath = '';
$sequence  = 0;
$metad     = '';
$metak     = '';
$active    = '';

/*
|--------------------------------------------------------------------------
| Edit Mode
|--------------------------------------------------------------------------
*/

if ($itemId) {

    $category = getRecord('categories', 'id', $itemId);

    if ($category) {

        $id        = (int) $category->id;
        $parent    = (int) ($category->parent_id ?? 0);
        $title     = $category->title ?? '';
        $slug      = $category->slug ?? '';
        $content   = $category->content ?? '';
        $imagepath = $category->imagepath ?? '';
        $sequence  = (int) ($category->sequence ?? 0);
        $metad     = $category->metad ?? '';
        $metak     = $category->metak ?? '';
        $active    = ((int)$category->active === 1)
                        ? 'checked="checked"'
                        : '';
    }
}

/*
|--------------------------------------------------------------------------
| Render
|--------------------------------------------------------------------------
*/

render('catform', [
    'page'      => $page,
    'images'    => $images,
    'pcats'     => $pcats,
    'id'        => $id,
    'parent'    => $parent,
    'title'     => $title,
    'slug'      => $slug,
    'content'   => $content,
    'imagepath' => $imagepath,
    'sequence'  => $sequence,
    'metad'     => $metad,
    'metak'     => $metak,
    'active'    => $active,
]);
?>