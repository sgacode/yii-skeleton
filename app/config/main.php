<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__) . DS . '..'));

require_once('params.php');

$config = array(
	
	// system
	'basePath' => ROOT,
	'name' => 'bs',
	'runtimePath' => ROOT . '/../data/tmp',

	// autoloading model and component classes
	'import' => array(
		'application.models.*',
		'application.components.*',
		'application.components.resources.*',
		'application.components.helpers.*',
		'application.components.widgets.*',
		'application.forms.*',
		'application.db.armodels.*',
		'application.db.gateways.*',
		'application.extensions.firephp.*',
	),
	
	'sourceLanguage' => 'ru',

	'defaultController' => 'main/index',

	'modules'=>array(
		'main',
		// remove in production
		'gii'=>array(
			'class' => 'system.gii.GiiModule',
			'password' => false,
			'ipFilters' => array('127.0.0.1', '192.168.2.*'),
		),
	),
	
	// application components
	'preload' => array('log'),
	'components' => array(
		'urlManager' => array(
			//'urlFormat' => 'path',
			'showScriptName' => false
		),
		'cache' => array(
            'class' => 'CMemCache',
			'keyPrefix' => $_SERVER['APP_NAME'] . '_',
            'servers' => array(
                array('host' => 'localhost', 'port' => 11211),
            ),
        ),
		'session' => array(
			'timeout' => 86400,
			'cookieMode' => 'only',
			'sessionName' => 'sid',
			'savePath' => ROOT . '/../data/sessions',
        ),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				// standard log route
				array(
					'class' => 'SFirePHPLogRoute', 
					'levels' => 'error, warning, info',
				),
				// profile log route
				array(
					'class' => 'SFirePHPProfileLogRoute',
					'report' => 'callstack',
				),
			),
		),
		// Объедение и сжатие скриптов/стилей
		'clientScript' => array(
			'class' => 'application.extensions.ExtendedClientScript.ExtendedClientScript',
			'excludeCssFiles' => array('theme.css'),
			'excludeJsFiles' => array('theme.js'),
			'scriptMap' => array(
				'jquery.js' => FALSE,
			),
		),
		// Определение браузера
		'browser' => array(
			'class' => 'application.extensions.browser.CBrowserComponent',
		),
	),
	// custom params
	'params' => $commonParams
);

return $config;
