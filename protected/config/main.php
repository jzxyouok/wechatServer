<?php
$params = require_once(__DIR__ . DIRECTORY_SEPARATOR . 'params.php');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'微信开发',

	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.wechat.*',
	),
	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Sogou2015',
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		'admin',
	),
	'components'=>array(
		'user'=>array(
			'allowAutoLogin'=>true,
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName' => false,        //去除index.php
			//'urlSuffix'=>'.html',           //加上.html
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'db' => array(
			'connectionString' => 'mysql:host=localhost;dbname=wechat',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '123456',
			'charset' => 'utf8',
		),
		'redis' => array(
			'class' => 'application.extensions.redis.ARedisConnection',
			'hostname' => 'localhost',
			'password' => 'mjpw_3202',
			'port' => 6379,
			'database' => 0,
			'prefix' => ''
		),
		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				//array(
				//	'class'=>'CWebLogRoute',
				//),
			),
		),
	),
	'params'=> $params,
);