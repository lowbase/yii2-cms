<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'eauth' =>  require(__DIR__ . '/eauth.php'),
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager'=>[
            /**
             * Включаем ЧПУ для всего проекта и убирем index.php
             */
            'enablePrettyUrl'=> true,
            'showScriptName'=> false,
            'rules' => [
                'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ]
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=dbname',
            'username' => 'username',
            'password' => 'password',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/../common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_MailTransport',

            ],
        ],
        'i18n' => [
            'translations' => [
                'eauth' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@eauth/messages',
                ],
            ],
        ],
    ],
];
