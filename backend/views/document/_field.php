<?php
/* @var $option_id integer */
/* @var $field_id integer */

use yii\helpers\Html;
use common\models\Document;
use common\models\User;
use kartik\widgets\Select2;
use mihaildev\elfinder\InputFile;
use kartik\widgets\FileInput;

echo "<a name='field-" . $field_id ."'></a>";
$class = 'form-group';
$name = $option['name'];
$error = null;
if ($option['require']) {
    if ($option['multiple']) {
        $name .= ' <span class="multiple-required-label">(обязательно хотя бы ' . $option['require'] . ')</span>';
        $class .= ' multiple-required';
    } else {
        $class .= ' required';
    }
}
if (isset($model) && in_array("fields[$option_id][value][$field_id]", array_keys($model->errors))) {
    $class .= ' has-error';
    $error = $model->errors["fields[$option_id][value][$field_id]"][0];
}
if (isset($model) && in_array("fields[$option_id][file][$field_id]", array_keys($model->errors))) {
    $class .= ' has-error';
    $error = $model->errors["fields[$option_id][file][$field_id]"][0];
}

echo "<div class='row'>";
if ($option['multiple']) {
    echo "<div class='col-sm-6'>";
} else {
    echo "<div class='col-sm-12'>";
}
$fields = 'fields[' . $option_id . '][value][' . $field_id . ']';
$value = (isset($model)) ? $model->fields[$option_id]['value'][$field_id] : '';
$position = (isset($model) && isset($model->fields[$option_id]['position'])) ? $model->fields[$option_id]['position'][$field_id] : '';
$box_start = "<div class='$class'><div>
                <label class='control-label' for='field-document-fields-$option_id-value-$field_id'>" . $name . "</label>
              </div><div>";
$box_end = "</div><div class='help-block'>" . $error . "</div></div>";
$select_setting = [
    'options' => [
        'id' => 'document-fields-' . $option_id . '-value-' . $field_id,
        'placeholder' => '',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'class' => 'form-control',
        'id' => 'document-fields-' . $option_id . '-value-' . $field_id,
    ]
];
if (isset($model)) {
    $select_setting['model'] = $model;
    $select_setting['attribute'] = $fields;
} else {
    $select_setting['name'] = 'Document[fields][' . $option_id . '][value][' . $field_id . ']';
}
if ($option['type'] >=1 && $option['type'] <= 11) {
    echo $box_start;
    switch ($option['type']) {
        case 1: // Целое число
        case 2: // Число
        case 3: // Строка
        case 11: // Регулярное выражение
            echo Html::input('text', "Document[fields][$option_id][value][$field_id]", $value, [
                'class' => 'form-control', 'id' => "field-document-fields-$option_id-value-$field_id"]);
            break;
        case 4: // Выключатеель
            $checked = (isset($model) && $model->fields[$option_id]['value'][$field_id]) ? true : false;
            echo Html::hiddenInput("Document[fields][$option_id][value][$field_id]", 0);
            echo Html::checkbox("Document[fields][$option_id][value][$field_id]", $checked, ["label" => null]);
            break;
        case 5: // Текст
            echo Html::textarea("Document[fields][$option_id][value][$field_id]", $value, [
                'class' => 'form-control', 'id' => "field-document-fields-$option_id-value-$field_id"]);
            break;
        case 6: // Файл (выбор)
            echo InputFile::widget([
                'language' => 'ru',
                'controller' => 'elfinder',
                'filter' => 'image',
                'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                'options' => ['class' => 'form-control', 'id' => "field-document-fields-$option_id-value-$field_id"],
                'buttonOptions' => ['class' => 'btn btn-default'],
                'buttonName' => 'Выбрать файл',
                'multiple' => false,       // возможность выбора нескольких файлов
                'name' => 'Document[fields][' . $option_id . '][value][' . $field_id . ']',
                'value' => $value,
            ]);
            break;
        case 7: // Изображение (загрузка)
            echo FileInput::widget([
                'name' => 'Document[fields][' . $option_id . '][file][' . $field_id . ']',
                'pluginOptions' => [
                    'browseClass' => 'btn btn-default',
                    'browseLabel' => 'Загрузить файл',
                    'removeLabel' => 'Удалить',
                    'removeClass' => 'btn btn-default',
                    'browseIcon' => '',
                    'removeIcon' => '',
                    'showUpload' => false
                ],
                'options' => [
                    'id' => 'document-fields-' . $option_id . '-file-' . $field_id
                ],
            ]);
            echo "<div class='help-block'>" . $error . "</div></div>";
            echo Html::hiddenInput("Document[fields][$option_id][value][$field_id]", $value, [
                'class' => 'form-control', 'id' => "field-document-fields-$option_id-value-$field_id"]);
            if (isset($model) && !$model->isNewRecord && isset($model->fields[$option_id]['value'][$field_id])
                && $model->fields[$option_id]['value'][$field_id]) {
                echo Html::img($model->fields[$option_id]['value'][$field_id], ['class' => 'doc_img img-thumbnail']);
                if (count($model->fields[$option_id]['value']) > $model->fields[$option_id]['require']) {
                    echo "<p>" .
                        Html::a('Удалить изображение', ['/document/deletefield', 'id' => $field_id], [
                            'class' => 'lnk delete_photo']) .
                    "</p>";
                }
            }
            echo "</div>";
            break;
        case 8: // Список дочерних документов
            $select_setting['data'] = Document::getChilds($option['param'], false);
            echo Select2::widget($select_setting);
            break;
        case 9: // Список потомков документа
            $select_setting['data'] = Document::getChilds($option['param'], true);
            echo Select2::widget($select_setting);
            break;
        case 10: // Список пользователей
            $select_setting['data'] = User::getAll();
            echo Select2::widget($select_setting);
            break;
    }
    if ($option['type'] != 7) {
        echo $box_end;
    }
}

echo "</div>";
if ($option['multiple']) {
    echo "<div class='col-sm-6'>
            <div class='form-group'>
                <div>";
    echo "<label class='control-label' for='field-document-fields-$option_id-position-$field_id'>Позиция</label>";
    echo "</div><div>";
    echo Html::input('text', "Document[fields][$option_id][position][$field_id]", $position, [
        'class' => 'form-control', 'id' => "field-document-fields-$option_id-position-$field_id"]);
    echo "</div></div></div>";
}
echo "</div>";
/**
 * Исключаем выполнение скрипта
 * при добавлении значения
 * мультиполя
 */
if (isset($model)) {
    $this->registerJs("
    var wait = false;
    var newfield = 0;
    $('.add-field').click(function(){
        var id = $(this).attr('id').substr(10);
        $.ajax({
            url: '/admin/document/ajaxoption',
            type: 'POST',
            data: {
                'id' : id,
                'newfield' : newfield
            },
            success: function(data){
                $('#field-'+id).append(data);
                newfield++;
            }
        });
    });
");
}
