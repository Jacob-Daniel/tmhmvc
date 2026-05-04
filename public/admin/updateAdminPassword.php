<?php

require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/admin_core/auth.php';

$config = getRecord('config', 'id', 1);
$error = null;
$token = $_GET['token'] ?? '';
$validReset = false;

global $db;

if ($token) {
    $tokenHash = $db->real_escape_string(hash('sha256', $token));
    $sql = "SELECT * FROM admin_password_resets 
            WHERE token_hash='$tokenHash' 
              AND used_at IS NULL 
              AND expires_at > NOW() 
            LIMIT 1";
    $res = $db->query($sql);
    if ($res && $res->num_rows > 0) {
        $validReset = true;
        $resetRecord = $res->fetch_assoc();
    } else {
        $error = "Invalid or expired reset link.";
    }
} else {
    $error = "No token provided.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? $password; // optional confirm field

    if (!$token || !$password) {
        $error = "Please provide a new password.";
    } else {
        $errors = validatePassword($password, $confirm);

        if ($errors) {
            $error = implode("<br>", $errors);
        } else {
            $tokenHash = $db->real_escape_string(hash('sha256', $token));
            $sql = "SELECT * FROM admin_password_resets 
                    WHERE token_hash='$tokenHash' 
                      AND used_at IS NULL 
                      AND expires_at > NOW() 
                    LIMIT 1";
            $res = $db->query($sql);

            if ($res && $res->num_rows > 0) {
                $resetRecord = $res->fetch_assoc();
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $_POST = ['password' => $passwordHash];
                updateRecord('adminusers', 'id', $resetRecord['admin_user_id']);

                $_POST = ['used_at' => date('Y-m-d H:i:s')];
                updateRecord('admin_password_resets', 'id', $resetRecord['id']);

                header("Location: login.php?message=Password updated successfully");
                exit;
            } else {
                $error = "Invalid or expired reset link.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?= $config->comp_name; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
$isDevMode = getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server';
?>
<?php if ($isDevMode): ?>
    <script type="module" src="http://localhost:3000/@vite/client"></script>
    <script type="module" src="http://localhost:3000/resources/js/admin/login.ts"></script>
<?php else: ?>
    <script type="module" src="<?= BASE_URL ?>/admin/dist/js/login.bundle.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL . '/admin/dist/css/main.css' ?>">    
<?php endif; ?>
</head>
<body class="flex flex-col w-full min-h-screen !bg-stone-100 items-center justify-center">  

<div class="p-10 flex flex-col gap-y-2 w-full md:w-1/3 border border-white bg-white h-[500px] bg-stone-50">

    <h2 class="text-center"><?= $config->comp_name; ?></h2>

    <?php if ($error): ?>
        <div class="text-amber-500"><?= $error; ?></div>
    <?php endif; ?>

    <?php if ($validReset): ?>

        <form method="post" class="flex flex-col gap-y-2">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

            <input class="border border-gray-400 p-2"
                   type="password"
                   name="password"
                   placeholder="New Password"
                   required>

            <button class="bg-stone-500 py-1 text-white cursor-pointer"
                    type="submit">
                Update Password
            </button>
        </form>

    <?php else: ?>
        <a href="login.php" class="text-blue-500 text-center">
            Return to login
        </a>
    <?php endif; ?>

</div>
</body>