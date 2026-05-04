<?php
declare(strict_types=1);

$itemId = filter_input(INPUT_GET, 'item', FILTER_VALIDATE_INT);
$rec    = null;

if ($itemId && $itemId > 0) {
    $rec = getRecord('images', 'id', $itemId);
}
$filename = $rec?->imagepath ?? '';

require __DIR__ . '/../../views/admin/imageform.php';

?>
