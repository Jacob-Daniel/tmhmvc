<?php
session_set_cookie_params(8 * 60 * 60);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../shared/config.php';
require_once __DIR__ . '/../shared/dbconfig.php';

$GLOBALS['db'] = new mysqli($hostname, $username, $password, $dbName);

if ($db->connect_errno > 0) {
    die("DB connection failed: " . $db->connect_error);
}

$db->set_charset('utf8mb4');

global $db;
$db = $GLOBALS['db'];

require_once __DIR__ . '/../shared/functions.php';

date_default_timezone_set("Europe/London");