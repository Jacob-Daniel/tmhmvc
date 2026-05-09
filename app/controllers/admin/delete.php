<?php
declare(strict_types=1);
header('Content-Type: application/json');

$id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$table = $_GET['table'] ?? '';

if (!$id || !$table) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$allowedTables = ['pages', 'categories', 'events', 'navigation', 'images'];

if (!in_array($table, $allowedTables, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid table.']);
    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch record metadata BEFORE delete
| - images  → need filename for file cleanup
| - others  → need slug for ISR path building
|--------------------------------------------------------------------------
*/
$recordMeta = null;

$metaFields = match ($table) {
    'images'     => 'imagepath',
    'pages'      => 'slug',
    'categories' => 'slug',
    'events'   => 'slug',
    default      => null,
};

if ($metaFields !== null) {
    $stmt = $db->prepare("SELECT $metaFields FROM `$table` WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recordMeta = $result->fetch_object();
    $stmt->close();
}

/*
|--------------------------------------------------------------------------
| Delete DB record
|--------------------------------------------------------------------------
*/
$stmt = $db->prepare("DELETE FROM `$table` WHERE id = ?");
$stmt->bind_param('i', $id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
    exit;
}
$stmt->close();

/*
|--------------------------------------------------------------------------
| Delete physical files (images only)
|--------------------------------------------------------------------------
*/
$filename = $recordMeta->imagepath ?? null;

if ($table === 'images' && !empty($filename)) {
    $safeFilename = basename(trim($filename));
    $base         = PUBLIC_UPLOADS_PATH . '/';
    $candidates   = [
        $base . $safeFilename,
        $base . 'facebook/'  . $safeFilename,
        $base . 'twitter/'   . $safeFilename,
        $base . 'webp/' . pathinfo($safeFilename, PATHINFO_FILENAME) . '.webp',
        $base . 'thumbs/150/' . $safeFilename,
        $base . 'thumbs/60/'  . $safeFilename,
    ];
    foreach ($candidates as $path) {
        if (is_file($path)) {
            if (!unlink($path)) {
                error_log("Failed to delete file: " . $path);
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| ISR Revalidation
|--------------------------------------------------------------------------
*/

$catSlug = '';
if ($table === 'events' && !empty($meta->slug)) {
    $cat = getRecord('categories', 'id', $meta->cat_id);
    $catSlug = $cat->slug ?? '';
}
$isrPathMap = [
    'pages'      => fn(object $meta) => ['/', '/pages/' . $meta->slug],
    'categories' => fn(object $meta) => ['/', '/categories/' . $meta->slug],
    'events'   => fn(object $meta) => ['/', '/whats-on/'.$catSlug.'/' . $meta->slug],
    'navigation' => fn(object $meta) => ['/'],
    'images'     => null, // no front-end path to bust
];

if (
    isset($isrPathMap[$table]) &&
    $isrPathMap[$table] !== null &&
    $recordMeta !== null
) {
    $paths = ($isrPathMap[$table])($recordMeta);

    require_once __DIR__ . '/../../shared/isr.php';
    revalidateISR($paths);
}

echo json_encode([
    'success' => true,
    'message' => ucfirst($table) . " record deleted successfully.",
    'id'      => $id,
]);