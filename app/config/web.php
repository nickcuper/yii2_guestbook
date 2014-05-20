<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'sourceLanguage' => 'en',
    'language' => 'ru',
    'modules' => [

		'users' => [
		    'class' => 'app\modules\users\Users'
		],

		'api' => [
			'class' => 'app\modules\api\Api'
		],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'request' => [
            'baseUrl' => '/',
            'class' => 'app\extensions\langrequestmanager\LangRequestManager',
        ],
        'urlManager' => [
            'baseUrl' => '',
            'class' => 'app\extensions\langurlmanager\LangUrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'enableStrictParsing' => true,
            'rules' => [

                    // Base
                    '' => 'site/index',
                    '<_a:(about|contact|error|captcha|state)>' => 'site/<_a>',
                    '<controller:\w+>/<id:\d+>'=>'<controller>/index',
                    '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                    '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',

                    // Module Users
                    '<_a:(login|logout|signup|activation|recovery|resend|avatar|partners|guestbook|comments)>' => 'users/default/<_a>',
                    'my/settings/<_a:[\w\-]+>' => 'users/default/<_a>',
                    '<_m:users>/<username:[a-zA-Z0-9_-]{3,20}+>' => '<_m>/default/view',

            ]
        ],

        'user' => [
                'class' => 'yii\web\User',
                'identityClass' => 'app\modules\users\models\User',
                'loginUrl' => ['/users/default/login']
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'i18n' => [
			'translations' => [
				'users' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'sourceLanguage' => 'ru',
					'basePath' => '@app/modules/users/messages',
				]
			]
		],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
