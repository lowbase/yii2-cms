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
 * Дополнительные поля шаблона (административная часть)
 * Class FieldController
 * @package app\modules\back_document\controllers
 */
class FieldController extends \lowbase\document\controllers\FieldController
{
    public $layout = '@app/admin/layouts/main.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete', 'multidelete'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['templateUpdate'],
                    ],
                ],
            ],
        ];
    }

}
