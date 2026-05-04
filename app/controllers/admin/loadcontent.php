<?php
$content = $_GET['content'];
switch($content)
{
	case 'config' :
		$config = getRecord('config','id',1);
		require_once("configform.php");
		break;
	default :
		require_once __DIR__.'/../../views/admin/'.$content.'.php';
}
?>
