<?php

namespace common\models;

use Yii;
use yii\validators\Validator;
use yii\imagine\Image;
use Imagine\Image\ManipulatorInterface;
use common\helpers\CFF;

/**
 * This is the model class for table "Field".
 *
 * @property integer $id
 * @property integer $option_id
 * @property integer $document_id
 * @property integer $porition
 * @property string $value
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * @var \yii\web\UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['option_id', 'document_id'], 'required'],
            [['option_id', 'document_id', 'position'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'option_id' => 'Дополнительное поле',
            'document_id' => 'Документ',
            'position' => 'Позиция',
            'value' => 'Значение',
            'file' => 'Файл',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Option::className(), array('id' => 'option_id'));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), array('id' => 'document_id'));
    }

    /**
     * @return bool
     * Динамическая валидация
     * @TODO  Углубленная валидация списков (с проверкой наличия базы), изображений
     */
    public function beforeValidate()
    {
        /** @var \common\models\Option $option */
        $option = Option::findOne($this->option_id);
        if ($option) {
            switch ($option->type) {
                case 1:   //число целое
                case 4:   //выключатель
                case 8:   //список дочерних документов
                case 9:   //список потомков документа
                case 10:  //список пользователей
                    $this->validators[] = Validator::createValidator('integer', $this, 'value');
                    break;
                case 2:   //число
                    $this->validators[] = Validator::createValidator('double', $this, 'value');
                    break;
                case 3:   //строка
                case 5:   //текст
                case 6:   //файл (выбор)
                    $this->validators[] = Validator::createValidator('string', $this, 'value');
                    break;
                case 7:   //изображение (загрузка)
                    $this->validators[] = Validator::createValidator('image', $this, 'file', [
                        'minHeight' => 100,
                        'skipOnEmpty' => true
                    ]);
                    break;
                case 11:    //регулярное выражение
                    $pattern = ($option->param) ? $option->param : '\w';
                    $this->validators[] = Validator::createValidator('match', $this, 'value', [
                        'pattern' => $pattern
                    ]);
                    break;
            }
            if ($option->require) {
                if ($option->type == 7) {
                    if (!$this->value) {
                        $this->validators[] = Validator::createValidator('required', $this, 'file');
                    }
                } else {
                    $this->validators[] = Validator::createValidator('required', $this, 'value');
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function savePhoto()
    {
        if ($this->file) {
            $this->deletePhoto();
            $ext = "." . end(explode(".", $this->file));
            if ($ext === ".") {
                $ext = '.jpg';
            }
            $path = Document::FROM_ADM_PATH . Document::FILES_PATH . $this->document_id . '/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $name = $this->document->alias . "-" . $this->id;
            $fullname = $path . $name . $ext;
            $this->file->saveAs($fullname);
            $this->file = $fullname;
            Image::thumbnail($fullname, 300, 200, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
                ->save($path . $name .'_thumb'. $ext, ['quality' => 100]);
            $this->value = Document::FILES_PATH . $this->document_id . '/' . $name . $ext;
            if (!$this->isNewRecord) {
                $db = Field::getDb();
                $db->createCommand()->update('field', [
                    'value' => Document::FILES_PATH . $this->document_id . '/' . $name . $ext], ['id' => $this->id])->execute();
            } else {
                $this->save();
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function deletePhoto()
    {
        if ($this->value) {
            if (file_exists(Document::FROM_ADM_PATH . $this->value)) {
                unlink(Document::FROM_ADM_PATH . $this->value);
            }
            $thumb = CFF::getThumb($this->value);
            if (file_exists(Document::FROM_ADM_PATH . $thumb)) {
                unlink(Document::FROM_ADM_PATH . $thumb);
            }
            $this->value = null;
        }
        return true;
    }
}
