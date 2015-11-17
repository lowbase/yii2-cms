<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

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
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'type')->dropDownList(AuthItem::getTypes()) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
