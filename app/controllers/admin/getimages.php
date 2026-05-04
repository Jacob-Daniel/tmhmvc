<?php
declare(strict_types=1);

$perPage = PER_PAGE;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$page = max(1, $page);
$offset = ($page - 1) * $perPage;

$fld = filter_input(INPUT_GET, 'fld', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'imagepath';
$val = filter_input(INPUT_GET, 'val', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$val = urldecode($val);

$allowedFields = ['imagepath','title','alt','description'];
if (!in_array($fld, $allowedFields, true)) $fld = 'imagepath';

if (!empty($val) && strlen($val) >= 3) {
    $searchTerm = '%' . $val . '%';
    $sql = "SELECT * FROM images 
            WHERE {$fld} LIKE ? OR title LIKE ? OR alt LIKE ? 
            ORDER BY title ASC
            LIMIT ? OFFSET ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('sssii', $searchTerm, $searchTerm, $searchTerm, $perPage, $offset);
} else {
    $sql = "SELECT * FROM images ORDER BY title ASC LIMIT ? OFFSET ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ii', $perPage, $offset);
}

$stmt->execute();
$images = $stmt->get_result();

require_once __DIR__ . '/../components/imageTable.php';
buildImageTable($images, true); // returns HTML for table body