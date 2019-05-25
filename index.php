<?php

define('DB_HOST','127.0.0.1');
define('DB_USER','root');
define('DB_PWD','RhEl7364B!');
define('DB_NAME','hrplatformDB');
define('DB_PORT','3306');
define('DB_TYPE','mysql');
define('DB_CHARSET','utf8');

define('APPLICATION_PATH', dirname(__FILE__));

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
