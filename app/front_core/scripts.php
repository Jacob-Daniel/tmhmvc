<?php 
$isDevMode = getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server';
 if ($isDevMode): 
?>
  <script type="module" src="http://localhost:3000/@vite/client"></script>
  <script type="module" src="http://localhost:3000/resources/js/front/main.js"></script>
<?php else: ?>
  <link rel="stylesheet" href="<?= BASE_URL . '/front/dist/css/main.css'?>">
  <script type="module" src="<?= BASE_URL . '/front/dist/js/main.bundle.js'?>"></script>
<?php endif; ?>
