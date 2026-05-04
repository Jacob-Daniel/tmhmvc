<?php
declare(strict_types=1);

header('Content-Type: application/json');

$id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$table = $_GET['table'] ?? '';

if (!$id || !$table) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request.'
    ]);
    exit;
}

$allowedTables = [
    'pages',
    'categories',
    'products',
    'navigation',
    'images'
];

if (!in_array($table, $allowedTables, true)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid table.'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Special handling for images (get filename first)
|--------------------------------------------------------------------------
*/

$filename = null;

if ($table === 'images') {
    $stmt = $db->prepare("SELECT imagepath FROM images WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
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
    echo json_encode([
        'success' => false,
        'error'   => 'Database error.'
    ]);
    exit;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Delete physical files (images only)
|--------------------------------------------------------------------------
*/

if ($table === 'images' && !empty($filename)) {

    $safeFilename = basename(trim($filename));
    $base         = PUBLIC_UPLOADS_PATH . '/';

    $candidates = [
        $base . $safeFilename,
        $base . 'facebook/'  . $safeFilename,
        $base . 'twitter/'   . $safeFilename,
        $base . 'webp/' . pathinfo($safeFilename, PATHINFO_FILENAME) . '.webp',
        $base . 'thumbs/200/' . $safeFilename,
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

echo json_encode([
    'success' => true,
    'message' => ucfirst($table) . " record deleted successfully.",
    'id'      => $id
]);