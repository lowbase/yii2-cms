<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\user;

/**
 * Модуль пользователя
 * унаследованный от модуля \lowbase\user\Module
 * Class Module
 * @package app\admin\user
 */
class Module extends \lowbase\user\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'app\admin\modules\user\controllers';

    /**
     * Инициализация
     */
    public function init()
    {
        parent::init();
    }
}
