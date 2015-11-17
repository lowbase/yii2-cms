<?php
use yii\helpers\Html;
use mihaildev\elfinder\InputFile;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use common\models\Document;
use common\models\User;

$option = 'option_' . $i;
$option_file = 'option_' . $i . '_file';
$option_require = 'option_' . $i . '_require';
$option_type = 'option_' . $i . '_type';
$option_param = 'option_' . $i . '_param';
$class = 'form-group field-document-option_' . $i;
$error = null;
if ($template->$option_require == 1) {
    $class .= ' required';
}
if (in_array($option, array_keys($model->errors))) {
    $class .= ' has-error';
    $error = $model->errors[$option][0];
}
if (in_array($option_file, array_keys($model->errors))) {
    $class .= ' has-error';
    $error = $model->errors[$option_file][0];
}
$options_attributes = [
    'class' => 'form-control',
    'id' => 'document-option_' . $i
];
if (isset ($empty_value) && $empty_value) {
    $options_attributes['value'] = '';
}

switch ($template->$option_type) {
    case 1: // Целое число
    case 2: // Число
    case 3: // Строка
    case 11: // Регулярное выражение
        echo "<div class='$class'><div>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            "</div><div>" .
            Html::activeInput('text', $model, $option, $options_attributes) .
            "</div><div class='help-block'>" . $error . "</div></div>";
        break;
    case 4: // Выключатель
        echo "<div class='$class'><div>" .
            Html::activeLabel($model, $option, ['class' => 'control-label', 'id' => 'document-option_'.$i]) .
            "</div><div>" .
            Html::activeCheckbox($model, 'option_'.$i, ['label'=>null]) .
            "</div><div class='help-block'>" . $error . "</div></div>";
        break;
    case 5: // Текст
        echo "<div class='$class'><div>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            "</div><div>" .
            Html::activeTextarea($model, $option, $options_attributes) .
            "</div><div class='help-block'>" . $error . "</div></div>";
        break;
    case 6: // Файл (выбор)
        echo "<div class='$class'><div>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            "</div><div>" .
            InputFile::widget([
                'language'   => 'ru',
                'controller' => 'elfinder',
                'filter'        => 'image',
                'template'      => '<div class="input-group">
                                        {input}<span class="input-group-btn">{button}</span>
                                    </div>',
                'options'       => $options_attributes,
                'buttonOptions' =>  ['class' => 'btn btn-default'],
                'buttonName'    => 'Выбрать файл',
                'name'       => 'Document[option_' . $i . ']',
                'value'      => $model->$option,
            ]) .
            "</div><div class='help-block'>" . $error . "</div></div>";
        break;
    case 7: // Изображение (загрузка)
        echo "<div class='$class'>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            FileInput::widget([
                'model' => $model,
                'attribute' => $option_file,
                'pluginOptions' => [
                    'browseClass' => 'btn btn-default',
                    'browseLabel' => 'Загрузить файл',
                    'removeLabel' => 'Удалить',
                    'removeClass' => 'btn btn-default',
                    'browseIcon' => '',
                    'removeIcon' => '',
                    'showUpload' => false
                ]
            ]) .
            Html::activeHiddenInput($model, $option_file, $options_attributes);
        if (!$model->isNewRecord && $model->$option && $model->last_template_id == $model->template_id) {
            echo Html::img($model->$option, ['class' => 'doc_img img-thumbnail']) .
                "<p>" .
                Html::a('Удалить изображение', [
                    '/document/deleteimg', 'document_id' => $model->id, 'option_id' => $i], [
                    'class' => 'lnk delete_photo']) .
                "</p>";
        }
        echo "<div class='help-block'>" . $error . "</div></div>";
        break;
    case 8: // Список дочерних документов
        echo "<div class='$class'>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            Select2::widget([
                'model' => $model,
                'attribute' => $option,
                'data' => Document::getChilds($template->$option_param, true),
                'options' => ['placeholder' => ''],
                'pluginOptions' => $options_attributes
            ]) .
            "<div class='help-block'>" . $error . "</div></div>";
        break;
    case 9: // Список потомков документов
        echo "<div class='$class'>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            Select2::widget([
                'model' => $model,
                'attribute' => $option,
                'data' => Document::getChilds($template->$option_param, false),
                'options' => ['placeholder' => ''],
                'pluginOptions' => $options_attributes
            ]) .
            "<div class='help-block'>" . $error . "</div></div>";
        break;
    case 10: // Список пользователя
        echo "<div class='$class'>" .
            Html::activeLabel($model, $option, ['class' => 'control-label']) .
            Select2::widget([
                'model' => $model,
                'attribute' => $option,
                'data' => User::getAll(),
                'options' => ['placeholder' => ''],
                'pluginOptions' => $options_attributes
            ]) .
            "<div class='help-block'>" . $error . "</div></div>";
        break;
}
