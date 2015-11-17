<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth_item".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 */
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
        ]];
    }

    public static function tableName()
    {
        return 'auth_item';
    }

    public static function getTypes()
    {
        return ['1' => 'Роль', '2' => 'Точка доступа'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'default', 'value' => 1],
            [['name', 'type'], 'required'],
            ['name', 'unique'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            ['description', 'default', 'value' => null],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Роль',
            'type' => 'Тип',
            'description' => 'Описание',
            'rule_name' => 'Название правила',
            'data' => 'Данные',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @param int $type
     * @param bool $empty
     * @return array
     * Получение массива ролей (type=1) и точек доступа (type=2)
     */
    public static function getAll($type = null, $key = 'name')
    {
        $roles=[];
        if ($type) {
            $role = AuthItem::find()->where(['type'=>$type])->all();
            if ($role) {
                foreach ($role as $r) {
                    if ($key == 'name') {
                        $roles[$r->name] = $r->name;
                    } elseif ($key == 'id') {
                        $roles[$r->id] = $r->name;
                    }
                }
            }
        } else {
            $role = AuthItem::find()->all();
            if ($role) {
                foreach ($role as $r) {
                    if ($r->type == 1) {
                        if ($key == 'name') {
                            $roles['Роли'][$r->name] = $r->name;
                        } elseif ($key == 'id') {
                            $roles['Роли'][$r->id] = $r->name;
                        }
                    } else {
                        if ($key == 'name') {
                            $roles['Точки доступа'][$r->name] = $r->name;
                        } elseif ($key == 'id') {
                            $roles['Точки доступа'][$r->id] = $r->name;
                        }
                    }
                }
            }
        }
        return $roles;
    }
}
