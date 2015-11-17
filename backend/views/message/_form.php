<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Message;
use common\models\User;
use common\models\Document;
use kartik\widgets\Select2;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model common\models\Message */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'message',
        'enableClientValidation' => false,
        'method' => 'POST',
        'options' => [
            'enctype'=>'multipart/form-data'
        ]
    ]);
    ?>

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-message"></i> <?= Html::a('Сообщения', [
                    '/message'])?> → <?= ($model->isNewRecord) ? "Создание" : "Редактирование"?>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">

            <div class="form-group row">
                <div class="col-sm-12">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-comment"></span> Отправить', [
                        'class' => 'btn btn-primary']) ?>
                    <?php
                    if (!$model->isNewRecord) {
                        echo Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                            'delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'status')->dropDownList(Message::getStatuses()) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'content')->textarea() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'attachment')->widget(InputFile::className(), [
                        'language'      => 'ru',
                        'controller'    => 'elfinder',
                        'template'      => '<div class="input-group">
                                                {input}<span class="input-group-btn">{button}</span>
                                            </div>',
                        'options'       => ['class' => 'form-control'],
                        'buttonName'    => 'Выбрать файл',
                        'buttonOptions' => ['class' => 'btn btn-default'],
                        'multiple'      => false
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'parent_message_id')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'for_user_id')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'data' => User::getAll(),
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'for_document_id')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'data' => Document::getAll(),
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <?php
                for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
                    echo "<div class='col-sm-12'>";
                    echo $form->field($model, 'option_' . $i)->textInput(['maxlength' => true]);
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
