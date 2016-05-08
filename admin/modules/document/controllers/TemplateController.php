<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\document\controllers;

use yii\filters\AccessControl;

/**
 * Шаблоны документов (административная часть)
 * Class TemplateController
 * @package app\modules\back_document\controllers
 */
class TemplateController extends \lowbase\document\controllers\TemplateController
{
    public $layout = '@app/admin/layouts/main.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'multidelete'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['templateManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['templateView'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['templateCreate'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['templateUpdate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['templateDelete'],
                    ],
                ],
            ],
        ];
    }
}
