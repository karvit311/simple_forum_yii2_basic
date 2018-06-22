<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'en-US',
    'sourceLanguage' => 'en-US',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'vote' => [
            'class' => hauntd\vote\Module::class,
            'guestTimeLimit' => 3600,
            'entities' => [
                  // Entity -> Settings
                'itemVote' => app\modules\forum\models\Forum::class, // your model
                'itemVoteGuests' => [
                    'modelName' => app\modules\forum\models\Forum::class, // your model
                    'allowGuests' => true,
                    'allowSelfVote' => false,
                    'entityAuthorAttribute' => 'user_id',
                ],
                'itemLike' => [
                    'modelName' => app\modules\forum\models\Forum::class, // your model
                    'type' => hauntd\vote\Module::TYPE_TOGGLE, // like/favorite button
                ],
                    'itemFavorite' => [
                        'modelName' => app\modules\forum\models\Forum::class, // your model
                        'type' => hauntd\vote\Module::TYPE_TOGGLE, // like/favorite button
                    ],
                ],
        ],
        // Module Kartik-v Grid
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],

        // Module Kartik-v Markdown Editor
        'markdown' => [
            'class' => 'kartik\markdown\Module',
        ],
        // Yii2 User
        'user' => [
            'class' => 'dektrium\user\Module',
            // Yii2 User Controllers Overrides
            'controllerMap' => [
                'admin' => 'cinghie\userextended\controllers\AdminController',
                'settings' => 'cinghie\userextended\controllers\SettingsController'
            ],
            // Yii2 User Models Overrides
            'modelMap' => [
                'RegistrationForm' => 'cinghie\userextended\models\RegistrationForm',
                'Profile' => 'cinghie\userextended\models\Profile',
                'SettingsForm' => 'cinghie\userextended\models\SettingsForm',
                'User' => 'cinghie\userextended\models\User',
            ],
        ],
        // Yii2 User Extended
        'userextended' => [
            'class' => 'cinghie\userextended\Module',
            'avatarPath' => '@webroot/img/users/', // Path to your avatar files
            'avatarURL' => '@web/img/users/', // Url to your avatar files
            'defaultRole' => '',
            'avatar' => true,
            'bio' => false,
            'captcha' => true,
            'birthday' => true,
            'firstname' => true,
            'gravatarEmail' => false,
            'lastname' => true,
            'location' => false,
            'onlyEmail' => false,
            'publicEmail' => false,
            'website' => false,
            'templateRegister' => '_two_column',
            'terms' => true,
            'showTitles' => true, // Set false in adminLTE
        ],
        'gridview' => ['class' => 'kartik\grid\Module'],
        'rbac' =>  [
            'class' => 'johnitvn\rbacplus\Module',
            'userModelClassName'=>null,
            'userModelIdField'=>'id',
            'userModelLoginField'=>'username',
            'userModelLoginFieldLabel'=>null,
            'userModelExtraDataColumls'=>null,
            'beforeCreateController'=>null,
            'beforeAction'=>null
        ],
        'forum' => [
            'class' => 'app\modules\forum\Module',
            'userModelClass' => 'dektrium\user\models\User',
        ],
        'message' => [
            'class' => 'thyseus\message\Module',
            'userModelClass' => 'dektrium\user\models\User', 
        ],
        'mailMessages' => function ($user) {
                    return $user->profile->receive_emails === true;
        },
        'recipientsFilterCallback' => function ($users) {
            return array_filter($users, function ($user) {
                return !$user->isAdmin;
            });
        },
    ],
    'components' => [
        'authClientCollection' => [
            'class'   => \yii\authclient\Collection::className(),
            'clients' => [
                'facebook' => [
                    'class'        => 'dektrium\user\clients\Facebook',
                    'clientId'     => '536833756652360',
                    'clientSecret' => '7e686813a99dbb3e9ac31dc4590a38c5',
                ],
                // here is the list of clients you want to use
                // you can read more in the "Available clients" section
            ],
        ],
        // Internationalization
        'i18n' => [
            'translations' => [
                'traits' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/cinghie/yii2-traits/messages',
                    // 'basePath' => 'app/messages',
                    // 'sourceLanguage' => 'en-US',
                    // 'language' => 'en-US',
                ],
                'vote' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@app/messages',
                ],
                'message' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@app/messages',
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/rbac/views' => '@vendor/cinghie/yii2-user-extended/views',
                    '@dektrium/user/views' => '@vendor/cinghie/yii2-user-extended/views',
                ],
            ],
        ],
        'authManager' => [
            'class' => 'dektrium\rbac\components\DbManager',
        ],
        
            // 'class' => 'yii\rbac\DbManager',
        'request' => [
            'cookieValidationKey' => 'AEwKM3LxQAbgqzb-qfEJIBguSjz6vvhV',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
       'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'gjtlff888',
                'password' => 'Re_zinaidaromanova311888',
                'port' => 587,
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
  
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}
 $config['components']['mailer'] = [
    'class' => 'yii\swiftmailer\Mailer',
];
return $config;
