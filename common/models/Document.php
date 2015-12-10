<?php

namespace common\models;

use Yii;
use yii\validators\Validator;
use paulzi\nestedintervals\NestedIntervalsBehavior;
use yii\imagine\Image;
use common\helpers\CFF;
use Imagine\Image\ManipulatorInterface;

/**
 * This is the model class for table "document".
 *
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property string $title
 * @property string $alias
 * @property integer $template_id
 * @property string $annotation
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $content
 * @property string $img
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_user_id
 * @property integer $updated_user_id
 * @property string $created_user_name
 * @property string $updated_user_name
 * @property integer $is_folder
 * @property integer $parent_id
 * @property string $parent_name
 * @property integer $root_id
 * @property string $root_name
 * @property string $option_1
 * @property string $option_2
 * @property string $option_3
 * @property string $option_4
 * @property string $option_5
 * @property string $option_6
 * @property string $option_7
 * @property string $option_8
 * @property string $option_9
 * @property string $option_10
 *
 * @property integer $last_parent_id
 * @property integer $last_template_id
 * @property array $fields
 */

class Document extends \yii\db\ActiveRecord
{
    const FROM_ADM_PATH = '../../frontend/web';
    const FILES_PATH = '/attaches/document/';

    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WITHOUT_NAV = 2;
    const STATUS_ONLY_NAV = 3;

    /**
     * @TODO Динамическое добавление свойств объекта в зависимости от кол-ва Template::OPTIONS_COUNT
     */
    public $option_1_file;
    public $option_2_file;
    public $option_3_file;
    public $option_4_file;
    public $option_5_file;
    public $option_6_file;
    public $option_7_file;
    public $option_8_file;
    public $option_9_file;
    public $option_10_file;

    public $fields = [];

    public $last_parent_id; //отслеживаем изменение родителя
    public $last_template_id; //отслежиаем изменения шаблона

    /**
     * @return array
     * Список статусов
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_BLOCKED => 'Скрыто',
            self::STATUS_ACTIVE => 'Опубликовано',
            self::STATUS_WITHOUT_NAV => 'Без навигации',
            self::STATUS_ONLY_NAV => 'Навигация'
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => NestedIntervalsBehavior::className(),
            ],
        ];
    }

    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'document';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        $rules = parent::rules();

        $options = [];
        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $options[] = 'option_' . $i;
        }

        $rules[] = [['created_at', 'created_user_id', 'created_user_name', 'alias', 'name',
            'parent_id', 'root_id', 'template_id', 'status'], 'required'];
        $rules[] = [['created_user_id', 'updated_user_id', 'template_id', 'parent_id', 'root_id',
            'is_folder', 'status','last_parent_id','last_template_id'], 'integer'];
        $rules[] = ['alias', 'unique'];
        $rules[] = [['annotation', 'content', 'meta_description',
            'meta_keywords'], 'string'];
        $rules[] = [['name', 'title', 'alias', 'img', 'created_user_name', 'updated_user_name',
            'root_name', 'parent_name'], 'string', 'max' => 255];
        $rules[] = ['status', 'in', 'range' => array_keys(self::getStatuses())];
        $rules[] = [['name', 'title', 'alias'], 'filter', 'filter' => 'trim'];
        $rules[] = [array_merge(['title', 'annotation', 'meta_description', 'meta_keywords', 'content',
            'img', 'updated_at', 'updated_user_id', 'updated_user_name', 'parent_name',
            'root_name'], $options), 'default', 'value' => null];
        $rules[] = [['status'], 'default', 'value' => self::STATUS_ACTIVE];
        $rules[] = [['parent_id'], 'default', 'value' => 1];
        $rules[] = [['is_folder'], 'default', 'value' => 0];
        $rules[] = [['created_at', 'updated_at', 'fields'], 'safe'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['id'] = 'ID';
        $labels['lft'] = 'Левый край диапазона';
        $labels['rgt'] = 'Правый край диапазона';
        $labels['depth'] = 'Уровень';
        $labels['name'] = 'Название';
        $labels['title'] = 'Заголовок';
        $labels['alias'] = 'Алиас';
        $labels['template_id'] = 'Шаблон';
        $labels['annotation'] = 'Аннотация';
        $labels['meta_description'] = 'Мета-описание';
        $labels['meta_keywords'] = 'Мета-ключи';
        $labels['content'] = 'Содержание';
        $labels['img'] = 'Изображение';
        $labels['status'] = 'Статус';
        $labels['created_at'] = 'Создан';
        $labels['updated_at'] = 'Обновлен';
        $labels['created_user_id'] = 'Создал';
        $labels['updated_user_id'] = 'Обновил';
        $labels['created_user_name'] = 'Создал';
        $labels['updated_user_name'] = 'Обновил';
        $labels['is_folder'] = 'Папка?';
        $labels['parent_id'] = 'Родительский документ';
        $labels['parent_name'] = 'Родительский документ';
        $labels['root_id'] = 'Корневой документ';
        $labels['root_name'] = 'Корневой документ';

        if ($this->template_id) {
            $template = Template::findOne($this->template_id);
        }

        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $option_name = 'option_' . $i . '_name';
            $labels['option_' . $i] = (isset($template->$option_name) && $template->$option_name) ?
                $template->$option_name : 'Опция ' . $i;
            $labels['option_' . $i . '_file'] = (isset($template->$option_name) && $template->$option_name) ?
                $template->$option_name : 'Опция ' .$i;
        }

        return $labels;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsercreated()
    {
        return $this->hasOne(User::className(), ['id' => 'created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserupdated()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::className(), ['id' => 'template_id']);
    }

    /**
     * @return $this
     */
    public function getFields()
    {
        return $this->hasMany(Field::className(), ['document_id' => 'id'])->orderBy('option_id, id');
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getOptions()
    {
        $options = Option::find()->where(['template_id' => $this->template_id])->orderBy('id')->all();
        return $options;
    }

    /**
     * Добавление недостающих значений поля fields.
     * Т.к. после load затираются значения массива,
     * не пришедшие с POST-данными.
     * Устранение несовершенства функции SetAttribute Yii2.
     * @return bool
     */
    public function loadOptions()
    {
        $options = $this->getOptions();
        $new = 0;
        foreach ($options as $option) {
            if (in_array($option->id, array_keys($this->fields))) {
                $this->fields[$option->id]['type'] = $option->type;
                $this->fields[$option->id]['require'] = $option->require;
                $this->fields[$option->id]['param'] = $option->param;
                $this->fields[$option->id]['multiple'] = $option->multiple;
                $this->fields[$option->id]['name'] = $option->name;
                $new++;
            }
        }
        return true;
    }

    /**
     * @param \common\models\Option $option
     * @param integer $new - id нового аттрибута
     * @return bool
     * Добавляем опцию из таблицы option к полю $fields документа
     */
    protected function addNewOption($option, $new)
    {
        if ($option) {
            $this->fields[$option->id]['type'] = $option->type;
            $this->fields[$option->id]['require'] = $option->require;
            $this->fields[$option->id]['param'] = $option->param;
            $this->fields[$option->id]['multiple'] = $option->multiple;
            $this->fields[$option->id]['name'] = $option->name;
            $this->fields[$option->id]['value']['new_' . $new] = '';
            if ($option->type == 7) {
                $this->fields[$option->id]['file']['new_' . $new] = '';
            }
            if ($option->multiple) {
                $this->fields[$option->id]['position']['new_' . $new] = '';
            }
            $new++;
            return $new;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     * Инициализация документа
     * Заполнение аттрибута fields
     */
    public function initialization()
    {
        $options = $this->getOptions();
        $this->fields = [];
        $new = 0; //для аттрибутов новых динамических полей
        /*
         * Заполнение уже имеющихся значений документа
         * из БД таблицы field.
         * Второе условие обеспечивает возможность сброса полей
         * при динамическом изменении шаблона документа
         */
        if (!$this->isNewRecord && $this->last_template_id == $this->template_id) {
            $fields = Field::find()
                ->where(['document_id' => $this->id])
                ->orderBy('option_id, id')
                ->all();
            if ($fields) {
                foreach ($fields as $field) {
                    if (isset($field->option)) {
                        $option_id = $field->option_id;
                        $this->fields[$option_id]['type'] = $field->option->type;
                        $this->fields[$option_id]['require'] = $field->option->require;
                        $this->fields[$option_id]['param'] = $field->option->param;
                        $this->fields[$option_id]['multiple'] = $field->option->multiple;
                        $this->fields[$option_id]['name'] = $field->option->name;
                        $this->fields[$option_id]['value'][$field->id] = $field->value;
                        //изображение (загрузка)
                        if ($this->fields[$option_id]['type'] == 7) {
                            $this->fields[$option_id]['file'][$field->id] = $field->file;
                        }
                        //мультиполе
                        if ($field->option->multiple) {
                            $this->fields[$option_id]['position'][$field->id] = $field->position;
                        }
                    }
                }
            }
        }
        /*
         * Добавление к массиву полей $fields новых аттрибутов
         * согласно данных текущего шаблона документа
         * и его дополнительных полей из таблицы option.
         */
        if ($options) {
            foreach ($options as $option) {
                /**
                 * Определяем присутствие хотя бы одного значения
                 * дополнительного поля.
                 */
                if (!in_array($option->id, array_keys($this->fields))) {
                    /**
                     * Добавляем обязательные для заполнения аттрибуты
                     * мультиполей.
                     */
                    if ($option->multiple && $option->require) {
                        for ($i = 1; $i <= $option->require; $i++) {
                            $new = $this->addNewOption($option, $new);
                        }
                    } else {
                        $new = $this->addNewOption($option, $new);
                    }
                } else {
                    /**
                     * Заполненные данные по текущему полю
                     * уже присутствуют,
                     * но их может оказаться не достаточно.
                     */
                    $option_fields =  Field::find()
                        ->where([
                            'document_id' => $this->id,
                            'option_id' => $option->id])
                        ->all();
                    $fields_count = count($option_fields);
                    $new_field_count = $option->require - $fields_count;
                    if ($new_field_count > 0) {
                        for ($i=1; $i <= $new_field_count; $i++) {
                            $new = $this->addNewOption($option, $new);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     * Установление родительского документа и его названия
     * и корневого документа и его названия
     */
    protected function setParentAndRoot()
    {
        if ($this->parent_id) {
            $parent = Document::findOne($this->parent_id);
            if ($parent) {
                $this->parent_name = $parent->name;
                if ($this->parent_id == 1) {    //корневой документ или папка (1 уровень)
                    $this->root_id = 0;
                    $this->root_name = null;
                } elseif ($this->parent_id > 1) {
                    if ($parent->depth == 1) {    //документы второго уровня
                        $this->root_id = $parent->id;
                        $this->root_name = $parent->name;
                    } elseif ($parent->depth >= 2) {
                        $this->root_id = $parent->root_id;
                        $this->root_name = $parent->root_name;
                    }
                }
            }
        }
        return true;
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

    /**
     * Владиация "быстрых" полей
     * @return bool
     */
    protected function optionValidate()
    {
        $template = Template::findOne($this->template_id);
        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $option_type = 'option_' . $i . '_type';
            $option_require = 'option_' . $i . '_require';
            $option_param = 'option_' . $i . '_param';
            if ($template && $template->$option_type) {
                switch ($template->$option_type) {
                    case 1:   //число целое
                    case 4:   //выключатель
                    case 8:   //список дочерних документов
                    case 9:   //список потомков документа
                    case 10:  //список пользователей
                        $this->validators[] = Validator::createValidator('integer', $this, 'option_'.$i);
                        break;
                    case 2:   //число
                        $this->validators[] = Validator::createValidator('double', $this, 'option_'.$i);
                        break;
                    case 3:   //строка
                    case 5:   //текст
                    case 6:   //файл (выбор)
                        $this->validators[] = Validator::createValidator('string', $this, 'option_'.$i);
                        break;
                    case 7:   //изображение (загрузка)
                        $this->validators[] = Validator::createValidator('image', $this, 'option_'.$i.'_file', [
                            'minHeight' => 100,
                            'skipOnEmpty' => true
                        ]);
                        break;
                    case 11:    //регулярное выражение
                        $pattern = ($template->$option_param) ? $template->$option_param : '/\w/';
                        $this->validators[] = Validator::createValidator('match', $this, 'option_'.$i, [
                            'pattern' => $pattern
                        ]);
                        break;
                }
                if ($template->$option_require) {
                    if ($template->$option_type == 7) {
                        $this->validators[] = Validator::createValidator('required', $this, 'option_'.$i.'_file');
                    } else {
                        $this->validators[] = Validator::createValidator('required', $this, 'option_'.$i);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Валидация дополнительных полей и файлов
     * @param array $count_fields - кол-во значений каждого поля
     * @param $option_id - тип дополнительного поля
     * @param $option - конфигурация поля
     * @param string $type (value|file)
     * @return array - кол-во значений каждого поля
     */
    protected function typeValidate($option_id, $option, $count_fields = [], $type = 'value')
    {
        if (isset($option[$type]) && $option[$type]) {
            //Перебираем все значения каждого поля
            foreach ($option[$type] as $field_id => $value) {
                if (substr_count($field_id, 'new')) {
                    $field = new Field();
                } else {
                    $field = Field::findOne($field_id);
                }
                $field->option_id = $option_id;
                $field->document_id = $this->id;
                $field->$type = $value;
                $field->position = isset($option['position'][$field_id]) ? $option['position'][$field_id] : null;
                if ($field->validate()) {
                    if (in_array($option_id, array_keys($count_fields))) {
                        $count_fields[$option_id]++;
                    } else {
                        $count_fields[$option_id] = 1;
                    }
                } else {
                    // значение поля или файл не прошло валидацию
                    if (isset($field->errors[$type][0])) {
                        $this->addError('fields[' . $option_id . '][' . $type . '][' . $field_id . ']', $field->errors[$type][0]);
                    }
                    // позиция значения не прошла валидацию
                    if (isset($field->errors['position'][0])) {
                        $this->addError('fields[' . $option_id . '][position][' . $field_id . ']', $field->errors['position'][0]);
                    }
                }
            }
        }
        return $count_fields;
    }

    /**
     * Нормализация ошибок модели (Исключение лишних, добавление новых)
     * Используется при наличии мультиполей.
     * @param array $count_fields_value - кол-во значений поля
     * @param array $count_fields_file - кол-во файлов в поле
     * @return bool
     */
    protected function fixErrors($count_fields_value = [], $count_fields_file = [])
    {
        /**
         * Исключаем ошибки обязательного заполнения
         * в мультиполях (в случае если заполнено
         * необходимое количество значений) посредством
         * очистки массива ошибок и заполнение его заново
         * посредством сравнения с количеством значений
         * дополнительного поля
         */
        $old_errors = $this->errors;
        $this->clearErrors();
        foreach ($old_errors as $field => $errors) {
            if ($errors[0] == 'Необходимо заполнить «Значение».') {
                $option_id = substr($field, 7, strpos($field, ']')-7);
                if ($this->fields[$option_id]['multiple']) {
                    if (!in_array($option_id, array_keys($count_fields_value))) {
                        $this->addError($field, $errors[0]);
                    } else {
                        if ($count_fields_value[$option_id] < $this->fields[$option_id]['require']) {
                            $this->addError($field, $errors[0]);
                        }
                    }
                } else {
                    $this->addError($field, $errors[0]);
                }
            } elseif ($errors[0] == 'Необходимо заполнить «Файл».') {
                $option_id = substr($field, 7, strpos($field, ']')-7);
                if ($this->fields[$option_id]['multiple']) {
                    if (!in_array($option_id, array_keys($count_fields_file))) {
                        $this->addError($field, $errors[0]);

                    } else {
                        if ($count_fields_file[$option_id] < $this->fields[$option_id]['require']) {
                            $this->addError($field, $errors[0]);
                        }
                    }
                } else {
                    $this->addError($field, $errors[0]);
                }
            } else {
                $this->addError($field, $errors[0]);
            }
        }
        /**
         * Добавляем ошибки обязательного заполнения в
         * мультиполях (если заполнено меньше значений,
         * чем необходимо). Блок не выполняется если не
         * произведена подмена POST-данных, т.к. в форме
         * добавляются пустые поля = кол-ву необходимых
         * для заполнения.
         */
        $error_msg = 'Недостаточно значений мультиполя ';
        foreach ($this->fields as $option_id => $option) {
            if (isset($count_fields_value[$option_id]) && !isset($count_fields_file[$option_id])) {
                if ($option['require'] > $count_fields_value[$option_id]) {
                    $this->addError('fields[' . $option_id . ']', $error_msg . $option['name']);
                }
            } elseif (isset($count_fields_file[$option_id])) {
                if ($option['require'] > $count_fields_file[$option_id]) {
                    $this->addError('fields[' . $option_id . ']', $error_msg . $option['name']);
                }
            } else {
                $this->addError('fields[' . $option_id . ']', $error_msg . $option['name']);
            }
        }
        return true;
    }

    /**
     * Валидация дополнительных полей
     * @return array - количество значений поля и файлов поля
     */
    protected function fieldsValidate()
    {
        $count_fields_value = []; //кол-во значений каждого поля
        $count_fields_file = [];  //кол-во файлов в каждом поле
        if ($this->fields) {
            foreach ($this->fields as $option_id => $option) {
                //Валидация всех значений дополнительного поля
                $count_fields_value = $this->typeValidate($option_id, $option, $count_fields_value, 'value');
                //Валидация всех файловых значений поля
                $count_fields_file = $this->typeValidate($option_id, $option, $count_fields_file, 'file');
            }
            if ($this->errors) {
                //Обязательная процедура нормализации для мультиполей
                $this->fixErrors($count_fields_value, $count_fields_file);
            }
        }
        return true;
    }

    /**
     * Защита от подмены параметров дополнительного поля
     * Эти ключи и значения не должны приниматья через POST
     * @return bool
     */
    public function injectValidate()
    {
        //доступные опции документа
        $true_options = $this->getOptionArray();
        $true_attr = ['require', 'multiple', 'type', 'name', 'param'];
        foreach ($this->fields as $option_id => $option) {
            // Защита от подмены данных формы
            if (!in_array($option_id, $true_options)) {
                $this->addError('fields', 'Дополнительное поле отсутствует');
            }
            foreach ($true_attr as $attr) {
                if (array_key_exists($attr, $option)) {
                    $this->addError('fields', 'Подмена данных поля '. $attr);
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->scenario != 'search') {
            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
            }
            $this->setAuthor();
            $this->setParentAndRoot();
        }
        $this->optionValidate();
        $this->fieldsValidate();
        return true;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            /**
             * Очистка старых значений "быстрых" полей
             * при смене шаблона документа.
             */
            if ($this->last_template_id !== $this->template_id) {
                $template = Template::findOne($this->template_id);
                if ($template) {
                    for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
                        $option_type = 'option_'.$i.'_type';
                        $option = 'option_'.$i;
                        if (!$template->$option_type) {
                            $this->$option = null;
                        }
                    }
                }
            }

            return true;
        }
        return false;
    }

    /**
     * * Сохранение значения(-ий) дополнительного поля
     * @param $option_id - Тип дополнительного поля
     * @param $option - Дополнительное поле (со структурой)
     * @param string $type (value|file)
     * @param $z
     * @return bool
     */
    protected function saveField($option_id, $option, $z, $type = 'value')
    {
        $x = 1;
        foreach ($option[$type] as $field_id => $value) {
            /** @var \common\models\Field $field[$z][$x] */
            if (substr_count($field_id, 'new')) {
                $field[$z][$x] = new Field();
            } else {
                $field[$z][$x] = Field::findOne($field_id);
            }
            if ($field[$z][$x]) {
                $field[$z][$x]->option_id = $option_id;
                $field[$z][$x]->document_id = $this->id;
                $field[$z][$x]->$type = $value;
                $field[$z][$x]->position = isset($option['position'][$field_id]) ? $option['position'][$field_id] : null;
                if ($field[$z][$x]->$type) {
                    $field[$z][$x]->save();
                    if ($type == 'file') {
                        $field[$z][$x]->savePhoto();
                    }
                } else {
                    if (!$field[$z][$x]->isNewRecord && $type == 'value') {
                        $field[$z][$x]->delete();
                    }
                }
            }
            $x++;
        }
        return true;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Делаем родительский документ папкой
        $db = Document::getDb();
        $db->createCommand()->update('document', ['is_folder' => 1], ['id' => $this->parent_id])->execute();
        // Произошла смена шаблона. Удаляем старые поля и изображения
        if ($this->last_template_id != $this->template_id) {
            CFF::RemoveDir(Document::FROM_ADM_PATH . Document::FILES_PATH . $this->id);
            Field::deleteAll('document_id = :document_id', [':document_id' => $this->id]);
        }
        if ($this->fields) {
//            exit(print_r($this->fields));
            $z = 1;
            foreach ($this->fields as $option_id => $option) {
                if (isset($option['value']) && $option['value']) {
                    $this->saveField($option_id, $option, $z, 'value');
                    $z++;
                }
                if (isset($option['file']) && $option['file']) {
                    $this->saveField($option_id, $option, $z, 'file');
                    $z++;
                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return array
     * Вывод всех документов с ID массивом
     */
    public static function getAll()
    {
        $documents = [];
        $models = Document::find()->all();
        if ($models) {
            foreach ($models as $model) {
                $documents[$model->id] = $model->name . " (" . $model->id . ")";
            }
        }
        return $documents;
    }

    /**
     * @return array
     * Вывод всех опций документа массивом
     */
    public function getOptionArray()
    {
        $options =[];
        $models = self::getOptions();
        if ($models) {
            foreach ($models as $model) {
                $options[] = $model->id;
            }
        }
        return $options;
    }

    /**
     * @param $id - ID документа источника
     * @param bool $children true - дочерние, false - потомки
     * @return array
     * Вывод всех дочерних документов / потомков с ID массивом
     * Используются методы компонента Nested Intervals
     */
    public static function getChilds($id, $children = true)
    {
        $childs = [];
        /** @var \paulzi\nestedintervals\NestedIntervalsBehavior $model */
        $model = Document::findOne($id);
        if ($model) {
            if ($children) {
                $ch = $model->getChildren()->all();
            } else {
                $ch = $model->getDescendants()->all();
            }
            if ($ch) {
                foreach ($ch as $c) {
                    $childs[$c->id] = $c->name . " (" . $c->id . ")";
                }
            }
        }
        return $childs;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * Сохранение файлов для возможных файлов "быстрых" полей
     */
    public function savePhoto()
    {
        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $option_file = 'option_' . $i .'_file';
            $option  = 'option_' . $i;
            if ($this->$option_file) {
                $this->deletePhoto($i);
                $ext = "." . end(explode(".", $this->$option_file));
                if ($ext === ".") {
                    $ext = '.jpg';
                }
                $name = $this->alias . "_" . $i;
                $path = self::FROM_ADM_PATH . self::FILES_PATH . $this->id . '/';
                $fullname = $path . $name . $ext;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $this->$option_file->saveAs($fullname);
                $this->$option_file = $fullname;
                Image::thumbnail($fullname, 300, 200, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
                    ->save($path . $name . '_thumb'. $ext, ['quality' => 100]);
                $this->$option = self::FILES_PATH . $this->id . '/' . $name . $ext;
                if (!$this->isNewRecord) {
                    $db = Document::getDb();
                    $db->createCommand()->update('document', [$option => $this->$option], [
                        'id' => $this->id
                    ])->execute();
                } else {
                    $this->save();
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return bool
     * Удаление файлов для возможных файлов "быстрых" полей
     */
    public function deletePhoto($id)
    {
        $option = 'option_' . $id;
        if (isset($this->$option) && $this->$option) {
            if (file_exists(self::FROM_ADM_PATH . $this->$option)) {
                unlink(self::FROM_ADM_PATH . $this->$option);
            }
            $thumb = CFF::getThumb($this->$option);
            if (file_exists(self::FROM_ADM_PATH . $thumb)) {
                unlink(self::FROM_ADM_PATH . $thumb);
            }
            $this->$option=null;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            //Удаляем дополнительные поля документа
            Field::deleteAll('document_id = :document_id', [':document_id' => $this->id]);
            //Удаляем папку со связанными файлами документа
            CFF::RemoveDir(Document::FROM_ADM_PATH . Document::FILES_PATH . $this->id);
            return true;
        } else {
            return false;
        }
    }
}
