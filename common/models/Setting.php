<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property string $name
 * @property string $title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $logo
 * @property string $favicon
 * @property string $copyright
 * @property string $counter
 * @property string $message_options_names
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meta_description', 'meta_keywords', 'copyright', 'counter', 'message_options_names'], 'string'],
            [['name', 'title', 'logo', 'favicon'], 'string', 'max' => 255],
            [['name', 'title', 'logo', 'favicon'], 'filter', 'filter' => 'trim']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Название сайта',
            'title' => 'Заголовок главной страницы',
            'meta_description' => 'Описание главной страницы',
            'meta_keywords' => 'Ключевые слова главной страницы',
            'logo' => 'Логотип',
            'favicon' => 'Иконка',
            'copyright' => 'Копирайт',
            'counter' => 'Счетчики статистики',
            'message_options_names' => 'Названия опций сообщений (через запятую)',
        ];
    }
}
