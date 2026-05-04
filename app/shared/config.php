<?php
$baseUrl = getenv('BASE_URL') ?: 'http://mvcl.uk';
define('BASE_URL', $baseUrl);
define('BASE_URL_IMG_DIR', $baseUrl.'/uploads');
define('BASE_PATH','/');
define('PER_PAGE', 20);
//jd123abc_0D
define('MAIL_CONFIG_SOURCE','db');
define('COMPANY_NAME','Theopi Skarlatos');
define("MAIL_PORT", 587);
define('MAIL_PASSWORD','');
define('MAIL_USERNAME','');
define('MAIL_SMTPHOST','');
define('MAIL_TO_ADDRESS','');
define('MAIL_FROM_ADDRESS','');
define('MAIL_FROM_NAME',COMPANY_NAME);
define("MAIL_SUBJECT", "Website Form Message");


define('ROOT_PATH', realpath(__DIR__ . '/../../'));
define('APP_PATH', ROOT_PATH . '/app');
define('ADMIN_PATH', APP_PATH . '/admin_core');
define('CONTROLLER_PATH', APP_PATH . '/controllers/admin');
define('SHARED_PATH', APP_PATH . '/shared');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('PUBLIC_UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('MAIL_TEMPLATES_PATH', APP_PATH . '/mail_templates');

?>