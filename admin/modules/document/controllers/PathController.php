<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\document\controllers;

use yii\filters\AccessControl;

class PathController extends \mihaildev\elfinder\PathController {

    public $root = [
        'baseUrl'=>'',
        'basePath'=>'@app/web',
        'path' => 'attach',
        'name' => 'Файлы',
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['manager', 'connect'],
                'rules' => [
                    [
                        'actions' => ['manager', 'connect'],
                        'allow' => true,
                        'roles' => ['fileManager'],
                    ],

                ],
            ],
        ];
    }

}
