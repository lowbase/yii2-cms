<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\models\User;
use common\models\AuthItem;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

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
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'email_confirm_token')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'gender')->dropDownList([
                '' => '',
                '1' => 'Мужской',
                '2' => 'Женский'
            ]); ?>
        </div>
        <div class="col-sm-6">
            <div class="form-group">

                <label>День рождения</label>

                <?php $birthday  =  ($model->birthday) ? $model->birthday : null;?>
                <?= DatePicker::widget([
                    'name' => 'User[birthday]',
                    'value' => $birthday,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true
                    ]
                ]); ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'role_id')->dropDownList(AuthItem::getAll(1, 'id')) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'status')->dropDownList(User::getStatusesArray()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'oauth_fb_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'fb_page')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'oauth_vk_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'vk_page')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'oauth_ok_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'ok_page')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?php
            echo $form->field($model, 'photo')->widget(InputFile::className(), [
                'language'      => 'ru',
                'controller'    => 'elfinder',
                'filter'        => 'image',
                'template'      => '<div class="input-group">
                                        {input}<span class="input-group-btn">{button}</span>
                                    </div>',
                'options'       => ['class' => 'form-control'],
                'buttonName'    => 'Выбрать файл',
                'buttonOptions' => ['class' => 'btn btn-default'],
                'multiple'      => false
            ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
