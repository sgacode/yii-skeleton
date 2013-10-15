<?php

return array(
	'components' => array(
		'db' => array(
			//db config
		),
		'db2' => array(
			//db2 config
		),
		'clientScript' => array(
			'class' => 'application.extensions.ExtendedClientScript.ExtendedClientScript',
			'combineCss' => TRUE,
			'compressCss' => TRUE,
			'combineJs' => TRUE,
			'compressJs' => TRUE,
		),
	)
);
