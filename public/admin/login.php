<?php 
require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/admin_core/auth.php';

$logout = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

if ($logout && (int)$logout === 1 ) {
    logout();
}

$config = getRecord('config','id',1);
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
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
    <div class="p-10 flex flex-col gap-y-2 w-full md:w-1/3 border border-white bg-white p-2 h-[500px] bg-stone-50 ">
        <h2 class="text-center"><?= $config->comp_name; ?></h2>

        <?php if (!empty($_GET['error'])): ?>
            <div class="bg-red-100 text-red-500 p-2"><?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['message'])): ?>
            <div class="bg-green-100 text-green-500 p-2"><?= htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>

        <form id="login" class="flex flex-col gap-y-2" method="post">
            <input type="hidden" name="login" value="1">
            <input class="border border-gray-400 p-2" type="email" name="email" required>
            <input class="border border-gray-400 p-2" type="password" name="password" required>
            <button class="bg-stone-500 py-1 text-white cursor-pointer" type="submit">Login</button>
            <div id="res"></div>
        </form>
        <p><a href="<?= BASE_URL?>/admin/resetpwd.php" class="text-sm text-blue-500 text-center mt-2">Forgot password?</a></p>
    </div>

</body>
</html>