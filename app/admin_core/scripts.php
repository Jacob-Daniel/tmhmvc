<script src="<?= BASE_URL ?>/admin/api/config.js.php"></script>

<?php 
$isDevMode = getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server';
?>
<?php if ($isDevMode): ?>
    <script type="module" src="http://localhost:3000/@vite/client"></script>
    <script type="module" src="http://localhost:3000/resources/js/admin/main.js"></script>
<?php else: ?>
    <link rel="stylesheet" href="<?= BASE_URL . '/admin/dist/css/main.css' ?>">
    <script type="module" src="<?= BASE_URL . '/admin/dist/js/main.bundle.js' ?>"></script>
<?php endif; ?>
