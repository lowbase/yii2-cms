<?php

use yii\helpers\Html;
use common\models\Message;
use common\models\User;
use common\models\Document;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\MessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-search">

    <div class="document-search">
        <div class="box box-panel collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="glyphicon glyphicon-search"></i> Фильтр расширенный
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body" style="display: none">

                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                ]); ?>

                <div class="form-group">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> Найти', [
                        'class' => 'btn btn-primary']) ?>
                    <?= Html::a('<span class="glyphicon glyphicon-repeat"></span> Сбросить', ['/message'], [
                        'class' => 'btn btn-default']) ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= FieldRange::widget([
                            'form' => $form,
                            'model' => $model,
                            'label' => 'ID',
                            'separator' => 'от ... до',
                            'attribute1' => 'id_from',
                            'attribute2' => 'id_till',
                            'type' => FieldRange::INPUT_TEXT,
                        ]);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status')->widget(Select2::classname(), [
                            'language' => 'ru',
                            'data' => Message::getStatuses(),
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
                    <div class="col-sm-6">
                        <?= FieldRange::widget([
                            'form' => $form,
                            'model' => $model,
                            'label' => 'Дата создания',
                            'separator' => 'от ... до',
                            'attribute1' => 'created_at_from',
                            'attribute2' => 'created_at_till',
                            'type' => FieldRange::INPUT_DATETIME,
                        ]);
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= FieldRange::widget([
                            'form' => $form,
                            'model' => $model,
                            'label' => 'Дата редактирования',
                            'separator' => 'от ... до',
                            'attribute1' => 'updated_at_from',
                            'attribute2' => 'updated_at_till',
                            'type' => FieldRange::INPUT_DATETIME,
                        ]);
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'created_user_id')->widget(Select2::classname(), [
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
                        <?= $form->field($model, 'updated_user_id')->widget(Select2::classname(), [
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
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'attachment')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'created_ip')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'for_user_id')->widget(Select2::classname(), [
                            'language' => 'ru',
                            'data' => User::getAll(),
                            'options' => [
                                'placeholder' => '',
                                'id' => 'for_user_id'
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
                                'id' => 'for_document_id'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'parent_message_id')->textInput(['maxlength' => true]) ?>
                    </div>
                    <?php
                    for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
                        echo "<div class='col-sm-6'>" .
                        $form->field($model, 'option_'.$i)->textInput(['maxlength' => true]) .
                        "</div>";
                    }
                    ?>
                 </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

</div>
