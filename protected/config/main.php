<?php
// Файл с настройками
$local_config = require(dirname(__FILE__).'/local/main.php');
$sms_config = require(dirname(__FILE__).'/local/sms.php');

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
        'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Query Agent',
        'sourceLanguage' => 'en',
        'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('bootstrap','log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.modules.user.models.*',
                'application.modules.user.components.*',
                'application.modules.respondent.models.*',
                'application.modules.respondent.components.*',
                'application.modules.catalog.models.*',
                'application.modules.licenses.models.*',
                'application.helpers.*',
                'ext.sms.smsext',
                // yii-EasyAPNs
                'application.modules.apns.*',
                'application.vendors.phpexcel.Classes.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'generatorPaths'=>array(
                            'bootstrap.gii',   // a path alias
                        ),
			'password'=>'0000',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),

                'user'=>array(
			// enable cookie-based authentication
			# encrypting method (php hash function)
                        'hash' => 'md5',

                        # send activation email
                        'sendActivationMail' => true,

                        # allow access for non-activated users
                        'loginNotActiv' => false,

                        # activate user on registration (only sendActivationMail = false)
                        'activeAfterRegister' => true,

                        # automatically login from registration
                        'autoLogin' => false,

                        # registration path
                        'registrationUrl' => array(),

                        # recovery password path
                        'recoveryUrl' => array('/recovery'),

                        # login form path
                        'loginUrl' => array('/login'),

                        # registration path
               			'registrationUrl' => array('/registration'),

                        # page after login
                        'returnUrl' => array('/site/index'),

                        # page after logout
                        'returnLogoutUrl' => array('/login'),
		),

                'respondent'=>array(
			// enable cookie-based authentication
			# encrypting method (php hash function)
                        'hash' => 'md5',
                        'defaultController' => 'respondent',
                ),

                'catalog',

                'licenses',

                'apns'=>array(
                        'class'=>'application.modules.apns.EasyAPNsModule',
                        'development' => $local_config['apns_type'],
                ),
	),

	// application components
	'components'=>array(
        'gcm' => array(
            'class' => 'ext.gcm.YiiGcm',
            'key' => 'AIzaSyCSkIJWssu9w7O7TyFoJpnhwQRD-rvAIsk'
        ),

        'bitly' => array(
            'class' => 'application.extensions.bitly.VGBitly',
            'login' => 'dutch92', // login name
            'apiKey' => 'R_32c7da3daa8340b8b7536b70540aecb2', // apikey
            'format' => 'json', // default format of the response this can be either xml, json (some callbacks support txt as well)
        ),

        'mybitly' => array(
            'class' => 'application.extensions.MyBitly.BitlyApiComponent',
            'access_token' => '7dafbce326d07e8a02aeaa682809da3f97cfdabb',
        ),

        'ePdf' => array(
            'class'         => 'ext.yii-pdf.EYiiPdf',
            'params'        => array(
                'mpdf'     => array(
                    'librarySourcePath' => 'application.vendors.mpdf.*',
                    'constants'         => array(
                        '_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
                    ),
                    'class'=>'mpdf', // the literal class filename to be loaded from the vendors folder
                    /*'defaultParams'     => array( // More info: http://mpdf1.com/manual/index.php?tid=184
                        'mode'              => '', //  This parameter specifies the mode of the new document.
                        'format'            => 'A4', // format A4, A5, ...
                        'default_font_size' => 0, // Sets the default document font size in points (pt)
                        'default_font'      => '', // Sets the default font-family for the new document.
                        'mgl'               => 15, // margin_left. Sets the page margins for the new document.
                        'mgr'               => 15, // margin_right
                        'mgt'               => 16, // margin_top
                        'mgb'               => 16, // margin_bottom
                        'mgh'               => 9, // margin_header
                        'mgf'               => 9, // margin_footer
                        'orientation'       => 'P', // landscape or portrait orientation
                    )*/
                )
            ),
        ),

        'request'=>array(
            'enableCookieValidation'=>true,
        ),
        /*'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'servers'=>$local_config['memcache_servers'],
        ),*/
        'bootstrap'=>array_merge(
                array(
                    'class'=>'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
                ), preg_match('/\/page\/.*/i', $_SERVER['REQUEST_URI']) ? array('yiiCss'=>false,'coreCss'=>false,'enableJS'=>false,) : array()
        ),

        'image'=>array(
                'class'=>'application.extensions.image.CImageComponent',
                // GD or ImageMagick
                'driver'=>'GD',
                // ImageMagick setup path
                //'params'=>array('directory'=>'/opt/local/bin'),
        ),

		'user'=>array(
                        'class' => 'WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
                        'loginUrl' => array('/login'),
		),

                'widgetFactory'=>array(
                    'widgets'=>array(
                        'QAQuestionaryWidget',
                        'QAMultipleConnectAudienceWidget',
                    ),
                ),

		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
                        'showScriptName'=>false,
			'rules'=>CMap::mergeArray(array(
                            'page/<view:\w+>' => 'site/page',

                            // Quiz and Missions
                            '/<type:(quiz|mission)>'=>'quiz/index',
                            '/<type:(quiz|mission)>/<archive:archive>'=>'quiz/index',
                            '/<type:(quiz|mission)>/<state:moderation>'=>'quiz/index',
                            '/<type:(quiz|mission)>/clone'=>'quiz/clone',
                            '/<type:(quiz|mission)>/<_a:(create)>'=>'quiz/<_a>',
                            '/<type:(quiz|mission)>/<_a:(precreate)>'=>'quiz/<_a>',
                            '/<type:(quiz|mission)>/<id:\d+>/<_a:(update|delete)>'=>'quiz/<_a>',
                            '/<type:(quiz|mission)>/<id:\d+>/comments'=>'quiz/comments',
                            '/<type:(quiz|mission)>/<id:\d+>/statistics'=>'quiz/statistics',
                            '/<type:(quiz|mission)>/<id:\d+>/export'=>'quiz/export',
                            '/<type:(quiz|mission)>/<id:\d+>/excel'=>'quiz/excel',
                            '/<type:(quiz|mission)>/<id:\d+>/<_a:(collection|launch)>'=>'quiz/<_a>',
                            '/<type:(quiz|mission)>/<quiz:\d+>/<controller:\w+>'=>'<controller>/index',
                            '/<type:(quiz|mission)>/<quiz:\d+>/<controller:\w+>/<id:\d+>'=>'<controller>/view',
                            '/<type:(quiz|mission)>/<quiz:\d+>/<controller:\w+>/<id:\d+>/<action:\w+>'=>'<controller>/<action>',
                            '/<type:(quiz|mission)>/<quiz:\d+>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                            '/<type:(quiz|mission)>/<quiz:\d+>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

                            // Respondent
                            '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                            '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                            '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                            '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

                            'gii'=>'gii',
                            'gii/<controller:\w+>'=>'gii/<controller>',
                            'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                            // LogIn
                            'login'=>'user/login',
                            'registration'=>'user/registration',
                            'registration_co'=>'user/registration/registration_co',
                            'thankyou'=>'user/registration/thankyou',
                            'recovery'=>'user/recovery',
                            'recovery/activkey/<activkey>/email/<email>'=>'user/recovery',
			), require('api/routes.php')),
		),

		/*'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database

		'db'=>$local_config['db'],
                'authManager'=>array(
                    'class'=>'CDbAuthManager',
                    'connectionID'=>'db',
                ),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
                            array(
                                'class'=>'CWebLogRoute',
                                'levels'=>'error, warning, trace, profile, info',
                                'enabled'=>false,
                            ),
                            array(
                                'class'=>'CFileLogRoute',
                                'levels'=>'error, warning',
                                'filter'=>'CLogFilter',
                            ),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
                            /*array( // -- CProfileLogRoute -----------------------
                                'class'=>'CProfileLogRoute',
                                'levels'=>'profile',
                                'enabled'=>true,
                            ),*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'adminEmail'=>'admin@queryagent.ru',
		// минимальная стоимость одного респондента в платных опросах
        'minCostRespondent'=>'20',
        // стоимость одного sms-сообщения
        'sendSmsCost'=>'1',
		// MerchantId в системе ASSIST
		'assistMerchantId'=>'578710',
		// Минимальная сумма пополнения
		'minimalSummPayment'=>'1',

		// какую комиссию в процентах мы забираем у каждого ответившего респондента
		'comissionOutRespondent'=>'50',

		// конфигурация sms-шлюзов
		'sms'=>$sms_config,
	),
);