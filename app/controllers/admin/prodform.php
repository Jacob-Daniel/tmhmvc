<?php
declare(strict_types=1);

$itemId    = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$delRaw    = filter_input(INPUT_GET, 'del');

if ($itemId && $delRaw) {
    $delIds = array_filter(
        array_map('intval', explode(',', $delRaw)),
        fn($v) => $v > 0
    );

    foreach ($delIds as $imageId) {
        $sql = "DELETE FROM prod_images WHERE prod_id = ? AND images_id = ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) throw new RuntimeException($db->error);

        $stmt->bind_param('ii', $itemId, $imageId);
        $stmt->execute();
        $stmt->close();
    }
}

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

$rec = null;

if ($itemId) {
    $rec = getRecord('products', 'id', $itemId);
}

$id              = $rec?->id ?? '';
$title           = $rec?->title ?? '';
$slug            = $rec?->slug ?? '';
$summary         = $rec?->summary ?? '';
$content         = $rec?->content ?? '';
$sequence        = $rec?->sequence ?? '';
$iframe          = $rec?->iframe ?? '';
$active          = ((int)($rec?->active ?? 0) === 1) ? 'checked="checked"' : '';
$iframe_active   = ((int)($rec?->iframe_active ?? 0) === 1) ? 'checked="checked"' : '';
$imagepath        = $rec?->imagepath ?? '';
$metak           = $rec?->metak ?? '';
$metad           = $rec?->metad ?? '';

$categories = getList("categories", " WHERE parent_id != 0 ORDER BY slug");

require __DIR__ . '/../../views/admin/components/imageModal.php';
require __DIR__ . '/../../views/admin/prodform.php';
?>