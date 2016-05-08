<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\user\controllers;

/**
 * Пользователи (административная часть)
 * Class UserController
 * @package app\modules\back_user\controllers
 */
class UserController extends \lowbase\user\controllers\UserController
{
    public $layout = '@app/admin/layouts/main.php';
}
