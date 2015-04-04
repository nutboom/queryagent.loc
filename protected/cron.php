<?php
error_reporting(E_ERROR);
/*
 * Файл cron.php запускает скрипты: php /path/to/cron.php test
 */


// change the following paths if necessary
$yiic = dirname(__FILE__).'/../yii/yiic.php';
$config = dirname(__FILE__).'/config/cron.php';

require_once($yiic);

?>
