<?php
declare(strict_types=1);

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/


if (isset($_POST['password']) && $_POST['password'] !== '') {
    $password = trim($_POST['password']);
    $confirm = trim($_POST['password_confirm']);

    $errors = [];
    if ($password !== $confirm) $errors[] = "Password must match.";
    if (strlen($password) < 10) $errors[] = "Password must be at least 10 characters.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Must contain at least one uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Must contain at least one lowercase letter.";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "Must contain at least one number.";
    if (!preg_match('/[`!@#$%^&*()_+\-=\[\]{}|;:\'"<>,.?\/~]/', $password)) $errors[] = "Must contain at least one special character.";

    if ($errors) {
        echo json_encode(['type'=>'error','message'=>implode("\n",$errors)]);
        exit;
    }

    $data['password'] = password_hash($password, PASSWORD_DEFAULT);
}


function normaliseSlug(string $value): string
{
    $value = strtolower(trim($value));

    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = preg_replace('/-+/', '-', $value);

    return trim($value, '-');
}

function getTableColumns(mysqli $db, string $table): array
{
    $result = $db->query("SHOW COLUMNS FROM `$table`");

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = [
            'type' => $row['Type'],        // e.g. int(11), varchar(255)
            'null' => $row['Null'] === 'YES',
        ];
    }

    return $columns;
}

function detectSlugField(array $columns): ?string
{
    foreach (['slug'] as $candidate) {
        if (isset($columns[$candidate])) {
            return $candidate;
        }
    }
    return null;
}

function ensureUniqueSlug(
    mysqli $db,
    string $table,
    string $slugField,
    string $slug,
    ?int $ignoreId = null
): string {
    $base = $slug;
    $i = 2;

    while (true) {
        $sql = "SELECT id FROM `$table` WHERE `$slugField` = ?";
        if ($ignoreId !== null) {
            $sql .= " AND id != ?";
        }

        $stmt = $db->prepare($sql);

        if ($ignoreId !== null) {
            $stmt->bind_param("si", $slug, $ignoreId);
        } else {
            $stmt->bind_param("s", $slug);
        }

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return $slug;
        }

        $slug = $base . '-' . $i++;
    }
}

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/

$table   = $_POST['table'] ?? '';
$id      = isset($_POST['edit']) ? (int)$_POST['edit'] : null;
$idField = $_POST['idfield'] ?? 'id';
// error_log("DEBUG TABLE: $table");

if (!$table) {
    exit('Invalid table');
}

$columns = getTableColumns($db, $table);
$slugField = detectSlugField($columns);

/*
|--------------------------------------------------------------------------
| Build Data Array
|--------------------------------------------------------------------------
*/

$data = [];

foreach ($_POST as $key => $value) {

    if (in_array($key, ['table','edit','idfield'], true)) continue;
    if (!isset($columns[$key])) continue;

    $colInfo = $columns[$key];
    $colType = $colInfo['type'];
    $isNullable = $colInfo['null'];

    $val = trim((string)$value);

    if (
        $id &&
        in_array($key, ['email_password', 'password'], true) &&
        $val === ''
    ) {
        continue;
    }    

    if (preg_match('/int/i', $colType)) {
        // INT column
        if ($val === '' && $isNullable) {
            $data[$key] = null; // bind as NULL
        } else {
            $data[$key] = (int)$val; // cast to int
        }
    } else {
        // default: string
        $data[$key] = $val;
    }
}

/*
|--------------------------------------------------------------------------
| Slug Handling
|--------------------------------------------------------------------------
*/

if ($slugField !== null) {

    if (empty($data[$slugField]) && isset($data['title'])) {
        $data[$slugField] = normaliseSlug($data['title']);
    } else {
        $data[$slugField] = normaliseSlug($data[$slugField] ?? '');
    }

    $data[$slugField] = ensureUniqueSlug(
        $db,
        $table,
        $slugField,
        $data[$slugField],
        $id
    );
}

/*
|--------------------------------------------------------------------------
| Insert or Update
|--------------------------------------------------------------------------
*/

if (isset($_POST['has_active_field'])) {
    $data['active'] = isset($_POST['active']) ? 1 : 0;
}

if ($id) {

    // UPDATE
    $set = [];
    $types = '';
    $values = [];

    foreach ($data as $col => $val) {
        $set[] = "`$col` = ?";

        if (is_int($val)) {
            $types .= 'i';
            $values[] = $val;
        } elseif ($val === null) {
            // nullable INT column, bind as integer null
            $types .= 'i';
            $values[] = null;
        } else {
            $types .= 's';
            $values[] = $val;
        }
    }

    $types .= 'i';
    $values[] = $id;

    $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE `$idField` = ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();

    $message = 'Updated successfully.';

} else {

    $columnsSql = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));

    $types = str_repeat('s', count($data));
    $values = array_values($data);

    $sql = "INSERT INTO `$table` ($columnsSql) VALUES ($placeholders)";

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();

    $message = 'Created successfully.';
}

$response = [
    'message' => $message, 
    'type' => 'success'  
];

echo json_encode($response);
exit;
