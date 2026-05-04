<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
require_once __DIR__ . '/../../views/admin/components/imageModalTable.php';

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$page = max(1, $page);

$perPage = 20;
$offset = ($page - 1) * $perPage;

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

if (!empty($search) && strlen($search) >= 3) {
    $searchTerm = '%' . $search . '%';
    $sql = "SELECT * FROM images
            WHERE imagepath LIKE ? OR title LIKE ?
            ORDER BY id DESC
            LIMIT ?, ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssii', $searchTerm, $searchTerm, $offset, $perPage);
} else {
    $sql = "SELECT * FROM images
            ORDER BY id DESC
            LIMIT ?, ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ii', $offset, $perPage);
}

$stmt->execute();
$images = $stmt->get_result();

echo buildImageModalTable($images);