<?php
$local_config = require(dirname(__FILE__).'/local/main.php');
$sms_config = require(dirname(__FILE__).'/local/sms.php');
// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Cron',
        'sourceLanguage' => 'en',
        'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),

        'import'=>array(
            'application.components.*',
            'application.models.*',
            'application.helpers.*',
            'application.modules.respondent.models.*',
            'application.modules.respondent.components.*',
            'application.modules.catalog.models.*',
        ),

        'modules'=>array(
            'respondent'=>array(
                    // enable cookie-based authentication
                    # encrypting method (php hash function)
                    'hash' => 'md5',
                    'defaultController' => 'respondent',
            ),

            'catalog',
        ),

	// application components
	'components'=>array(
		'db'=>$local_config['db'],
		'log'=>array(
                    'class'=>'CLogRouter',
                    'routes'=>array(
                        array(
                            'class'=>'CFileLogRoute',
                            'logFile'=>'cron.log',
                            'levels'=>'error, warning',
                        ),
                        array(
                            'class'=>'CFileLogRoute',
                            'logFile'=>'cron_trace.log',
                            'levels'=>'trace',
                        ),
                    ),
                ),
	),
        'params'=>CMap::mergeArray(array(
		// this is used in contact page
		'adminEmail'=>'admin@cavegroups.com',
                ), $sms_config
	),
);