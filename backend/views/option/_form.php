<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Option;
use common\models\Template;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\Option */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="option-form">


    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group row">
        <div class="col-sm-12">
            <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить', [
                'class' => 'btn btn-primary']) ?>
            <?php
            if (!$model->isNewRecord) {
                echo Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                    'delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
            }
            ?>
        </div>
    </div>

    <?php
    if ($model->multiple) {
        $class_require = 'form-group';
        $class_is_reqire = 'form-group hidden-block';
    } else {
        $class_require = 'form-group hidden-block';
        $class_is_reqire = 'form-group';
    }
    ?>


    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'template_id')->dropDownList(Template::getAll()); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'type')->dropDownList(Option::getTypesField()); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'param')->textInput(['maxlength' => true]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'multiple')->widget(SwitchInput::classname(), [
                'pluginOptions' => [
                    'onText' => 'Да',
                    'offText' => 'Нет',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => 'function(events, state) {
                        var is_req = $("#is_req");
                        if (state) {
                            is_req.hide();
                            $("#req").show();
                        } else {
                            is_req.show();
                            $("#req").hide();
                            var count = $("#req input").val();
                            if (count == "" || count =="0") {
                                $(is_req).find(".bootstrap-switch input").bootstrapSwitch("state", false);
                            } else {
                                $(is_req).find(".bootstrap-switch input").bootstrapSwitch("state", true);
                            }
                        }
                    }',
                ],
            ]); ?>
        </div>
        <div class="col-sm-6">
                <?= $form->field($model, 'require', ['options' => [
                    'class'=>$class_require,
                    'id' => 'req'
                ]])->textInput(['maxlength' => true]); ?>
                <?= $form->field($model, 'is_require', ['options' => [
                    'class'=>$class_is_reqire,
                    'id' => 'is_req'
                ]])->widget(SwitchInput::classname(), [
                    'pluginOptions' => [
                        'onText' => 'Да',
                        'offText' => 'Нет',
                    ]
                ]);
                ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>