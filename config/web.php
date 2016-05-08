<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'lowbase',
    'name' => 'lowBase',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'ISfNWi2OD58V6WoC8fYVx0q28RaiilRr',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //-----------------------
        // Компонент пользователя
        //-----------------------
        'user' => [
            'identityClass' => 'lowbase\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/login'],
            'on afterLogin' => function($event) {
                lowbase\user\models\User::afterLogin($event->identity->id);
            }
        ],
        //--------------------------------------------------------
        // Компонент OAUTH для авторизации через социальные сети,
        // где вмето ? указываем полученные после регистрации
        // клиентский ID и секретный ключ.
        // В комментария указаны ссылки для регистрации приложений
        // в соответствующих социальных сетях.
        //--------------------------------------------------------
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    // https://vk.com/editapp?act=create
                    'class' => 'lowbase\user\components\oauth\VKontakte',
                    'clientId' => '?',
                    'clientSecret' => '?',
                    'scope' => 'email'
                ],
                'google' => [
                    // https://console.developers.google.com/project
                    'class' => 'lowbase\user\components\oauth\Google',
                    'clientId' => '?',
                    'clientSecret' => '?',
                ],
                'twitter' => [
                    // https://dev.twitter.com/apps/new
                    'class' => 'lowbase\user\components\oauth\Twitter',
                    'consumerKey' => '?',
                    'consumerSecret' => '?',
                ],
                'facebook' => [
                    // https://developers.facebook.com/apps
                    'class' => 'lowbase\user\components\oauth\Facebook',
                    'clientId' => '?',
                    'clientSecret' => '?',
                ],
                'github' => [
                    // https://github.com/settings/applications/new
                    'class' => 'lowbase\user\components\oauth\GitHub',
                    'clientId' => '?',
                    'clientSecret' => '?',
                    'scope' => 'user:email, user'
                ],
                'yandex' => [
                    // https://oauth.yandex.ru/client/new
                    'class' => 'lowbase\user\components\oauth\Yandex',
                    'clientId' => '?',
                    'clientSecret' => '?',
                ],
            ],
        ],
        //---------------------------------------------
        // Для реализации разделения прав пользователей
        // с помощью коробочного модуля Yii2 RBAC.
        //---------------------------------------------
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'lb_auth_item',
            'itemChildTable' => 'lb_auth_item_child',
            'assignmentTable' => 'lb_auth_assignment',
            'ruleTable' => 'lb_auth_rule'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
        'mailer' => require(__DIR__ . '/mailer.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                //СЛУЖЕБНЫЕ ФУНКЦИИ ДЛЯ КЛИЕНТСКОЙ И АДМИНИСТРАТИВНОЙ ЧАСТИ САЙТА
                //Авторизация через социальные сети
                'auth/<authclient:[\w\-]+>' => 'lowbase-user/auth/index',
                'captcha' => 'lowbase-user/default/captcha',
                //Поиск населенного пункта (города)
                'city/find' => 'lowbase-user/city/find',

                //АДМИНИСТРАТИВНАЯ ЧАСТЬ САЙТА
                'admin' => 'admin/index',
                //Взаимодействия с пользователем в панели админстрирования
                'admin/user/<action:(index|update|delete|view|rmv|multidelete|multiactive|multiblock)>' => 'admin-user/user/<action>',
                //Взаимодействия со странами в панели админстрирования
                'admin/country/<action:(index|create|update|delete|view|multidelete)>' => 'admin-user/country/<action>',
                //Взаимодействия с городами в панели администрирования
                'admin/city/<action:(index|create|update|delete|view|multidelete)>' => 'admin-user/city/<action>',
                //Работа с ролями и разделением прав доступа
                'admin/role/<action:(index|create|update|delete|view|multidelete)>' => 'admin-user/auth-item/<action>',
                //Работа с правилами контроля доступа
                'admin/rule/<action:(index|create|update|delete|view|multidelete)>' => 'admin-user/auth-rule/<action>',
                //Взаимодействия с шаблонами в панели администрирования
                'admin/template/<action:(index|create|update|delete|view|multidelete)>' => 'admin-document/template/<action>',
                //Взаимодействия с документами в панели администрирования !!! Правила для документов лучше не менять, т.к. на них завязан js скрипт компонента дерево документов
                'admin/document/<action:(index|create|update|delete|view|multidelete|multiactive|multiblock|move|change|field)>' => 'admin-document/document/<action>',
                //Взаимодействия с файловым менеджеромч
                'elfinder/<action(connect|manager)>' => 'admin-document/path/<action>',
                //Взаимодействия с дополнительными полями шаблонов
                'admin/field/<action:(create|update|delete|multidelete)>' => 'admin-document/field/<action>',

                //КЛИЕНТСКАЯ ЧАСТЬ САЙТА
                //Взаимодействия с пользователем на сайте
                '<action:(login|logout|signup|confirm|reset|profile|remove|online)>' => 'user/<action>',
                //Просмотр пользователя
                'user/<id:\d+>' => 'user/show',
                // Лайк документа
                'like/<id:\d+>' => 'document/like',
                //Отображение документов
                '<alias>' => 'document/show',
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/lowbase',
                    'forceTranslation' => true,
                    'fileMap' => [
                        'document' => 'document/messages/document.php',
                        'user' => 'document/messages/user.php'
                    ]
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'admin' => [
            'class' => 'app\admin\controllers\AdminController',
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'lowbase-user' => [
            'class' => '\lowbase\user\Module',
        ],
        'lowbase-document' => [
            'class' => '\lowbase\document\Module',
        ],
        'admin-user' => [
            'class' => 'app\admin\modules\user\Module',
        ],
        'admin-document' => [
            'class' => 'app\admin\modules\document\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
