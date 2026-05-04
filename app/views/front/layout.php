<?php 
$isDevMode = getenv('APP_ENV') === 'development' || php_sapi_name() === 'cli-server';
 if ($isDevMode): 
?>
  <script type="module" src="http://localhost:3000/@vite/client"></script>
  <script type="module" src="http://localhost:3000/app/js/site.js"></script>
<?php else: ?>
  <link rel="stylesheet" href="<?= BASE_URL . '/dist/css/site.css'?>">
  <script type="module" src="<?= BASE_URL . '/dist/js/site.bundle.js'?>"></script>
<?php endif; ?>
