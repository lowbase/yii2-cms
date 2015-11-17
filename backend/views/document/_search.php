<?php

use yii\helpers\Html;
use common\models\Template;
use common\models\Document;
use common\models\User;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\DocumentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

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
                <?= Html::a('<span class="glyphicon glyphicon-repeat"></span> Сбросить', ['/document'], [
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
                        'data' => Document::getStatuses(),
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
                        'label' => 'Дата обновления',
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
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'annotation')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'parent_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'root_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'template_id')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'data' => Template::getAll(),
                        'options' => [
                            'placeholder' => ' ',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'img')->textInput(['maxlength' => true]) ?>
                </div>
                <?php
                for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
                     echo "<div class='col-sm-3'>" .
                         $form->field($model, 'option_'.$i)->textInput(['maxlength' => true]) .
                         "</div>";
                }
                ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
