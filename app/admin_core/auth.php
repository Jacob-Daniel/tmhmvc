<?php
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === 1;
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php?logout=1');
        exit;
    }
}

function handleLogin() {
    global $db;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login'])) {
        return false;
    }

    $_SESSION['failed_count'] ?? $_SESSION['failed_count'] = 0;

    if($_SESSION['failed_count'] >=5 && $_SESSION['failed_time'] + 360 > time()) {
        return false;
    }

    if (
        isset($_SESSION['failed_time']) &&
        $_SESSION['failed_time'] + 360 <= time()
    ) {
        $_SESSION['failed_count'] = 0;
    }    

    $user = trim($_POST['email'] ?? $_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? $_POST['passwd'] ?? '');

    $user = $db->real_escape_string($user);

    $res = $db->query("SELECT * FROM adminusers WHERE email='$user' OR name='$user'");
    if (!$res) {
        return false;
    }

    if ($res->num_rows) {
        $rec = $res->fetch_object();
        if (password_verify($password, $rec->password)) {
            $_SESSION['admin_name'] = $rec->username;
            $_SESSION['admin_logged_in'] = 1;
            $_SESSION['admin_user_id'] = $rec->id;
            $_SESSION['admin_id'] = $rec->id;
            $_SESSION['direction'] = 'asc';
            return true;
        }
    }
    $_SESSION['failed_count']++;
    $_SESSION['failed_time'] = time();
    $_SESSION['failed_lock'] = $_SESSION['failed_count'] >= 5 ?? 0;
    return false;
}

function logout() {
    session_unset();
    session_destroy();
}
