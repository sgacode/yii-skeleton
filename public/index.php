<?php
ob_start();
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: ' . date('r'));
$yii = dirname(__FILE__) . '/../lib/yii/yii.php';
// Set configurations based on environment
if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] != 'production')
{
	ini_set('display_errors','On'); 
	error_reporting(E_ALL); 
	
	defined('YII_DEBUG') or define('YII_DEBUG', TRUE);
	$environment = $_SERVER['APPLICATION_ENV'];
}
else
{
	$environment = 'production';
}

// Include Yii framework
require_once($yii);

// Include config files
$configMain = require_once(dirname(__FILE__) . '/../app/config/main.php');
// Disable caching in development
if ($environment != 'production')
{
	$configMain['components']['cache']['enabled'] = FALSE;
}
$configServer = require_once(dirname(__FILE__) . '/../app/config/server.' . $environment .'.php');
$config = CMap::mergeArray($configMain, $configServer);
// Run application
$app = Yii::createWebApplication($config)->run();
