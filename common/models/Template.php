<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "template".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 * @property string $option_1_name
 * @property string $option_1_type
 * @property string $option_1_param
 * @property string $option_2_name
 * @property string $option_2_type
 * @property string $option_2_param
 * @property string $option_3_name
 * @property string $option_3_type
 * @property string $option_3_param
 * @property string $option_4_name
 * @property string $option_4_type
 * @property string $option_4_param
 * @property string $option_5_name
 * @property string $option_5_type
 * @property string $option_5_param
 * @property string $option_6_name
 * @property string $option_6_type
 * @property string $option_6_param
 * @property string $option_7_name
 * @property string $option_7_type
 * @property string $option_7_param
 * @property string $option_8_name
 * @property string $option_8_type
 * @property string $option_8_param
 * @property string $option_9_name
 * @property string $option_9_type
 * @property string $option_9_param
 * @property string $option_10_name
 * @property string $option_10_type
 * @property string $option_10_param
 */
class Template extends \yii\db\ActiveRecord
{
    const OPTIONS_COUNT = 10; //полей в базе

    /**
     * @return array
     */
    public static function getTypesField()
    {
        return [
            '1' => 'Целое число',
            '2' => 'Число',
            '3' => 'Строка',
            '4' => 'Выключатель',
            '5' => 'Текст',
            '6' => 'Файл (выбор)',
            '7' => 'Изображение (загрузка)',
            '8' => 'Список дочерних документов',
            '9' => 'Список потомков документа',
            '10' => 'Список пользователей',
            '11' => 'Регулярное выражение'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [['name'], 'required'];
        $rules[] = [['name'], 'unique'];

        $options_name = [];
        $options_type = [];
        $options_param = [];
        $options_require = [];
        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $options_name[] = 'option_' . $i . '_name';
            $options_type[] = 'option_' . $i . '_type';
            $options_param[] = 'option_' . $i . '_param';
            $options_require[] = 'option_' . $i . '_require';
        }

        $rules[] = [array_merge(['name','path'], $options_name, $options_param), 'string', 'max' => 255];
        $rules[] = [array_merge($options_type, $options_require), 'integer'];
        $rules[] = [array_merge($options_name, ['name']), 'filter', 'filter' => 'trim'];
        $rules[] = [$options_require, 'default','value' => 0];
        $rules[] = [array_merge($options_type, $options_name, $options_param), 'default','value' => null];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['id'] = 'ID';
        $labels['name'] = 'Название';
        $labels['path'] = 'Путь к файлу';

        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $labels['option_' . $i . '_name'] = 'Название поля ' . $i;
            $labels['option_' . $i . '_type'] = 'Тип поля ' . $i;
            $labels['option_' . $i . '_require'] = 'Обязательность поля ' . $i;
            $labels['option_' . $i . '_param'] = 'Параметр поля ' . $i;
        }

        return $labels;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        $templates = [];
        $model = Template::find()->all();
        if ($model) {
            foreach ($model as $m) {
                $templates[$m->id] = $m->name . " (" . $m->id . ")";
            }
        }
        return $templates;
    }

    /**
     * Формирование массива аттрибутов согласно количеству
     * "быстрых" расширенный полей.
     * @param $attr - аттрибут (name|type|reuire|param|file)
     * @return mixed
     */
    public static function getOptionArray($attr)
    {
        $option = [];
        for ($i = 1; $i <= self::OPTIONS_COUNT; $i++) {
            $option[] = 'option_' . $i . '_' . $attr;
        }
        return $option;
    }
}
