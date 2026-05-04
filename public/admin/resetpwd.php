<?php
require_once __DIR__ . '/../../app/bootstrap/app.php';
require_once __DIR__ . '/../../app/admin_core/auth.php';
require_once __DIR__ . '/../../app/shared/sendresetmail.php';

$config = getRecord('config', 'id', 1);

?>
<!DOCTYPE html>
<html>
<head>
<title><?= $config->comp_name; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php $isDevMode = getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server'; ?>
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
        <form id="reset-form" method="post" class="flex flex-col gap-y-2" >
            <input type="email" name="email" class="border border-gray-400 p-2" placeholder="Admin email" required>
            <button class="bg-stone-500 py-1 text-white cursor-pointer" type="submit">Send Reset Link</button>
            <output id="result" class="border p-2 hidden"></output>
        </form>
</div>
</body>
</html>