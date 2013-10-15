<?php

return array(
	'components' => array(
		'messages'=>array(
            'onMissingTranslation' => array('HTr', 'logMissTr'),
        ),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning',
					'categories' => 'MainModule.main',
					'logFile' => 'trs.log'
				)
			)
		),
		array(
			'class' => 'CFileLogRoute',
			'levels' => 'error, warning',
			'categories'=>'MainModule.main',
			'logFile' => 'trs.log'
		),
		'db' => array(
			//db config
		),
		'db2' => array(
			//db2 config
		),
		'clientScript' => array(
			'class' => 'application.extensions.ExtendedClientScript.ExtendedClientScript',
			'combineCss' => FALSE,
			'compressCss' => FALSE,
			'combineJs' => FALSE,
			'compressJs' => FALSE,
		),
	)
);
