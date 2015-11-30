<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Europe/Moscow',
    'controllerNamespace' => 'backend\controllers',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'request'=>[
            'class' => 'common\components\Request',
            'web'=> '/backend/web',
            'adminUrl' => '/admin',
            'cookieValidationKey' => 'enter-your-validation-key'
        ],
        'urlManager'=>[
            /**
             * Включаем ЧПУ для всего проекта и убирем index.php
             */
            'enablePrettyUrl'=> true,
            'showScriptName'=> false,
            'rules' => [
                '<action:(login|logout|index|setting|manager)>'=>'site/<action>',
                'permission'=>'authitemchild/index',
                '<controller:(permission)>/<action:(index|create|update|delete|multidelete)>'=>'authitemchild/<action>',
                'role'=>'authitem/index',
                '<controller:(role)>/<action:(index|create|update|delete|multidelete)>'=>'authitem/<action>',
                'user'=>'user/index',
                '<controller:(user)>/<action:(index|view|create|update|delete|multidelete|multiopen|multiclose)>'=>'<controller>/<action>',
                'template'=>'template/index',
                '<controller:(template)>/<action:(index|create|update|delete|multidelete)>'=>'<controller>/<action>',
                '<controller:(option)>/<action:(create|update|delete|multidelete)>'=>'<controller>/<action>',
                'document'=>'document/index',
                '<controller:(document)>/<action:(index|create|update|multipublicate|multiclose|ajaxoptions|ajaxoption|deleteimg|deletefield|delete|multidelete|view)>'=>'<controller>/<action>',
                'message'=>'message/index',
                '<controller:(message)>/<action:(index|create|update|multipublicate|multiclose|deleteattachment|delete|multidelete)>'=>'<controller>/<action>',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],

    'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\PathController',
            'access' => ['@'],
            'root' => [
                'baseUrl'=>'',
                'basePath'=>'@webroot/../../frontend/web',
                'path' => '/attaches/',
                'name' =>'Файлы'
            ],
        ],
        'front-elfinder' => [
            'class' => 'mihaildev\elfinder\PathController',
            'access' => ['@'],
            'root' => [
                'baseUrl'=>'',
                'basePath'=>'@webroot/../../frontend/',
                'path' => '/views/',
                'name' =>'Отображения'
            ],
        ]
    ],

    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
//        'debug' => [
//            'class' => 'yii\debug\Module',
//            'allowedIPs' => ['*']
//        ],
//        'gii' => [
//            'class' => 'yii\gii\Module',
//            'allowedIPs' => ['*']
//        ],
    ],
    'params' => $params,
];


