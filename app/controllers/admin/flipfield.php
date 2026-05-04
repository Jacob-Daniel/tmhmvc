<?php
declare(strict_types=1);

header('Content-Type: application/json');

$id    = filter_input(INPUT_GET, 'pid', FILTER_VALIDATE_INT);
$field = $_GET['field'] ?? '';
$table = $_GET['table'] ?? '';

if (!$id || !$field || !$table) {
    echo json_encode(['message' => 'Invalid request.', 'type' => 'error']);
    exit;
}

$allowedTables = [
    'pages'      => ['featured', 'active'],
    'categories' => ['featured', 'active'],
    'products'   => ['featured', 'active'],
    'navigation' => ['active'],
];

if (!isset($allowedTables[$table])) {
    echo json_encode(['message' => 'Invalid table.', 'type' => 'error']);
    exit;
}

if (!in_array($field, $allowedTables[$table], true)) {
    echo json_encode(['message' => 'Invalid field.', 'type' => 'error']);
    exit;
}

$rec = getRecord($table, 'id', $id);

if (!$rec) {
    echo json_encode(['message' => 'Record not found.', 'type' => 'error']);
    exit;
}

// Toggle the value
$current = (int)($rec->$field ?? 0);
$newval  = $current === 1 ? 0 : 1;

$stmt = $db->prepare("UPDATE `$table` SET `$field` = ? WHERE id = ?");
$stmt->bind_param('ii', $newval, $id);

if (!$stmt->execute()) {
    error_log("EXEC ERROR: " . $stmt->error);
    echo json_encode(['message' => 'Database error.', 'type' => 'error']);
    exit;
}

$stmt->close();

echo json_encode([
    'flag'    => $newval ? 'Y' : 'N',
    'message' => 'Field updated successfully',
    'type'    => 'success'
]);
exit;