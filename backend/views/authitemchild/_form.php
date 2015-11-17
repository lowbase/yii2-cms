<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\AuthItem;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItemChild */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-child-form">

            <?php $form = ActiveForm::begin(); ?>

            <div class="form-group row">
                <div class="col-sm-12">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить', [
                        'class' => 'btn btn-primary']) ?>
                    <?php
                    if (!$model->isNewRecord) {
                        echo Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'parent')->widget(Select2::classname(), [
                        'data' => AuthItem::getAll(1),
                        'options' => ['placeholder' => ' '],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'child')->widget(Select2::classname(), [
                        'data' => AuthItem::getAll(),
                        'options' => ['placeholder' => ' '],
                        'pluginOptions' => [
                            'allowClear' => true
                        ]
                    ]); ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

</div>
