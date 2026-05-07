<?php
declare(strict_types=1);

$rec = null;
$token = '';
$created_at = '';

$result = $db->query("SELECT * FROM api_tokens WHERE label = 'default' LIMIT 1");
if ($result && $row = $result->fetch_object()) {
    $rec = $row;
    $token = $rec->token ?? '';
    $created_at = $rec->created_at ?? '';
}

render('apitokenform', [
    'rec'        => $rec,
    'token'      => $token,
    'created_at' => $created_at,
]);