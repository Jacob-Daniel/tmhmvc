<?php
declare(strict_types=1);

header('Content-Type: application/json');

$message = '';
$type = 'error';

$id = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
$fld = $_GET['fld'] ?? '';
$table = $_GET['table'] ?? '';
$val = $_GET['val'] ?? '';

if (!$id || !$fld || !$table) {
    echo json_encode([
        'message' => 'Invalid request.',
        'type' => 'error'
    ]);
    exit;
}

$allowedTables = [
    'pages' => ['title', 'slug', 'active', 'sequence'],
    'categories' => ['title', 'slug', 'active', 'sequence'],
    'products' => ['title', 'slug', 'active', 'sequence'],
    'navigation' => ['label', 'active', 'sequence']
];

if (!isset($allowedTables[$table])) {
    echo json_encode(['message' => 'Invalid table.', 'type' => 'error']);
    exit;
}

if (!in_array($fld, $allowedTables[$table], true)) {
    echo json_encode(['message' => 'Invalid field.', 'type' => 'error']);
    exit;
}

$rec = getRecord($table,'id',$id);

if (!$rec || !$rec->id) {
    echo json_encode(['message' => 'Record not found.', 'type' => 'error']);
    exit;
}

$sql = "UPDATE `$table` SET `$fld` = ? WHERE id = ?";
$stmt = $db->prepare($sql);

if (!$stmt) {
    error_log("PREPARE ERROR: " . $db->error);
    echo json_encode(['message' => 'System error.', 'type' => 'error']);
    exit;
}

$stmt->bind_param('si', $val, $id);

if (!$stmt->execute()) {
    error_log("EXEC ERROR: " . $stmt->error);
    echo json_encode([
        'message' => 'There was an error updating the system.',
        'type' => 'error'
    ]);
    exit;
}

if ($stmt->affected_rows === 0) {
    $message = 'No changes made.';
    $type = 'info';
} else {
    $message = 'Updated successfully.';
    $type = 'success';
}

$stmt->close();

echo json_encode([
    'message' => $message,
    'type' => $type
]);

exit;
