<?php
$fld = $_GET['fld'] ?? 'slug';
$val = isset($_GET['val']) ? urldecode($_GET['val']) : '';
$table = $_GET['table'] ?? 'products';
$perPage = PER_PAGE;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$allowedFields = ['slug', 'title', 'cat_id'];
if (!in_array($fld, $allowedFields, true)) {
    die("Invalid search field");
}

$params = [];
$types = "";
$sql = "SELECT * FROM $table";

if ($val !== '') {
    $sql .= " WHERE $fld LIKE ?";
    $params[] = '%' . $val . '%';
    $types .= "s";
}

$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";

$stmt = $db->prepare($sql);
if ($stmt === false) die("Prepare failed: " . $db->error);

$stmt->bind_param($types, ...$params);

$stmt->execute();
$products = $stmt->get_result();
$stmt->close();

echo buildProductTable($products);