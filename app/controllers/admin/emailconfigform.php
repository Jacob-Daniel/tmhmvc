<?php
$tableConfigs = require APP_PATH . '/shared/table_configs.php';
$config       = $tableConfigs['config_email'];
$rec          = getRecord('config_email', 'id', 1);

render('emailconfigform', [
    'rec'    => $rec,
    'config' => $config,
]);