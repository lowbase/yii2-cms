<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
namespace app\admin\modules\document\models;

use lowbase\user\models\User;

/**
 * Связываем Документы с Пользователями
 * Class Document
 * @package app\admin\modules\document\models
 */
class Document extends \lowbase\document\models\Document
{
    public function getCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdated()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
