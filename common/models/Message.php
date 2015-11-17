<?php

namespace common\models;

use Yii;
use common\helpers\CFF;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $attachment
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property string $created_user_name
 * @property string $updated_user_name
 * @property integer $for_document_id
 * @property integer $for_user_id
 * @property integer $parent_message_id
 * @property string $created_ip
 * @property string $option_1
 * @property string $option_2
 * @property string $option_3
 *
 * @property Document $forDocument
 * @property User $forUser
 * @property Message $parentMessage
 * @property Message[] $messages
 */
class Message extends \yii\db\ActiveRecord
{
    const OPTIONS_COUNT = 3; //кол-во доп. полей

    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_VISITED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_BLOCKED => 'Скрыто',
            self::STATUS_ACTIVE => 'Опубликовано',
            self::STATUS_VISITED => 'Просмотрено',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $options = [];
        for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
            $options[] = 'option_' . $i;
        }

        $rules[] = [['created_user_id', 'created_user_name', 'created_ip'], 'required'];
        $rules[] = [array_merge(['content'], $options), 'string'];
        $rules[] = [['status', 'created_user_id', 'updated_user_id', 'for_document_id',
            'for_user_id', 'parent_message_id'], 'integer'];
        $rules[] = [['title', 'attachment', 'created_user_name', 'updated_user_name'], 'string', 'max' => 255];
        $rules[] = [['created_ip'], 'string', 'max' => 19];
        $rules[] = [['content', 'title'], 'filter', 'filter' => 'trim'];
        $rules[] = [['status'], 'default', 'value' => self::STATUS_ACTIVE];
        $rules[] = [array_merge(['title', 'content', 'attachment', 'updated_at',
            'updated_user_id', 'updated_user_name', 'for_document_id', 'for_user_id',
            'parent_message_id'], $options), 'default', 'value' => null];
        $rules[] = [['created_at', 'updated_at'], 'safe'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' => 'ID',
            'title' => 'Заголовок',
            'content' => 'Сообщение',
            'attachment' => 'Вложение',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Отредактирован',
            'created_user_id' => 'Создал',
            'updated_user_id' => 'Отредактировал',
            'created_user_name' => 'Создал',
            'updated_user_name' => 'Отредактировал',
            'for_document_id' => 'Принадлежность документу',
            'for_user_id' => 'Принадлежность пользователю',
            'parent_message_id' => 'Родительское сообщение (ID)',
            'created_ip' => 'IP создателя',
        ];

        /** @var \common\models\Setting $setting */
        $setting = Setting::find()->one();
        if ($setting && $setting->message_options_names) {
            $names = explode(',', $setting->message_options_names);
        }

        for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
            $item = $i - 1;
            $labels['option_' . $i] = (isset($names[$item])) ?
                $names[$item] : 'Опция ' . $i;
        }

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'for_document_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForUser()
    {
        return $this->hasOne(User::className(), ['id' => 'for_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'parent_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['parent_message_id' => 'id']);
    }

    /**
     * @return bool
     * Установление автора и редактора документа
     */
    protected function setAuthor()
    {
        if (!Yii::$app->user->isGuest) {
            /** @var \common\models\Field $identity */
            $identity = Yii::$app->getUser()->getIdentity();
            if ($this->isNewRecord) {
                $this->created_user_id = $identity->id;
                $this->created_user_name = $identity->first_name;
                if ($identity->last_name) {
                    $this->created_user_name .= " " . $identity->last_name;
                }
            } else {
                $this->updated_user_id = $identity->id;
                $this->updated_user_name = $identity->first_name;
                if ($identity->last_name) {
                    $this->updated_user_name .= " " . $identity->last_name;
                }
            }
        } else {
            if ($this->isNewRecord) {
                $this->created_user_id = 0;
                $this->created_user_name = "Гость";
            } else {
                $this->updated_user_id = 0;
                $this->updated_user_name = "Гость";
            }
        }
        return true;
    }

    public function beforeValidate()
    {
        if ($this->scenario != 'search') {
            $this->setAuthor();
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_ip = CFF::getIP();
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
            }
            if (!$this->for_document_id && !$this->for_user_id) {
                $this->addError('for_document_id', 'Обязательна принадлежность
                 сообщения или пользователю или документу');
                $this->addError('for_user_id', 'Обязательна принадлежность
                 сообщения или пользователю или документу');
            }
        }

        return true;
    }
}
