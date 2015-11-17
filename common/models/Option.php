<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "option".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $param
 * @property integer $require
 * @property integer $multiple
 * @property integer $template_id
 */
class Option extends \yii\db\ActiveRecord
{
    public $is_require;

    /**
     * @return array
     */
    public static function getTypesField()
    {
        return Template::getTypesField();
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'option';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'template_id'], 'required'],
            [['type', 'require', 'multiple', 'template_id', 'is_require'], 'integer'],
            [['name', 'param'], 'string', 'max' => 255],
            [['name'], 'filter', 'filter' => 'trim'],
            [['require', 'multiple'], 'default', 'value' => 0],
            [['param'], 'default', 'value' => null]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'type' => 'Тип',
            'param' => 'Параметр',
            'require' => 'Количество обязательных полей',
            'is_require' => 'Обязательность заполнения',
            'multiple' => 'Множественная запись',
            'template_id' => 'Шаблон',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), array('id' => 'template_id'));
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->scenario != 'search') {
            if ($this->is_require && !$this->require) {
                $this->require = 1;
            }
            if (!$this->multiple) {
                $this->require = ($this->is_require) ? 1 : 0;
            }
        }
        return true;
    }

    /**
     * @param null $template_id
     * @return array
     */
    public static function getAll($template_id = null)
    {
        $options = [];
        if ($template_id) {
            $model = Option::find()->where(['template_id' => $template_id])->all();
        } else {
            $model = Option::find()->all();
        }
        if ($model) {
            foreach ($model as $m) {
                $options[$m->id] = $m->name;
            }
        }
        return $options;
    }
}
