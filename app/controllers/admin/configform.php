<?php
$config = getRecord('config','id',1);
$sc = (isset($config->show_cust) && $config->show_cust > 0) ? ' checked="checked"' : '';
$ue = (isset($config->use_events) && $config->use_events > 0) ? ' checked="checked"' : '';
$images = getlist('images', 'order by id');

render('configform', [
    'images' => $images,
    'config' => $config,
    'title'   => 'Settings',    
]);
?>