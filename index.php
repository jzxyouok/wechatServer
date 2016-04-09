<?php
date_default_timezone_set('PRC');
$yii = dirname(__FILE__).'/../yii/framework/yii.php';
$config = dirname(__FILE__).'/protected/config/main.php';
$global = dirname(__FILE__).'/protected/config/global.php';

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($global);
require_once($yii);
Yii::createWebApplication($config)->run();
