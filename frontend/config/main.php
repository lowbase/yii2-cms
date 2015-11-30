<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Europe/Moscow',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
        'request'=>[
            'class' => 'common\components\Request',
            'web'=> '/frontend/web',
            'cookieValidationKey' => 'enter-your-validation-key'
        ],
        'urlManager'=>[
            'enablePrettyUrl'=> true,
            'showScriptName'=> false,
            'rules' => [
                '<action:(login|logout)>'=>'site/<action>',
                '<action:(login)>/<service:(vkontakte|facebook|odnoklassniki)>' => 'site/<action>',
                'registration'=>'user/create',
                'me'=>'user/me',
                '<action:(confirm|repass|newpass)>'=>'user/<action>',
                '<action:(enable|disable)>/<service:(vkontakte|facebook|odnoklassniki)>' => 'user/<action>',
            ]
        ],
    ],
    'params' => $params,
];
