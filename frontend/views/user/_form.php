<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php
     $form = ActiveForm::begin(['options' => ['class'=>'form-signin']]);

    echo $form->field($model, 'first_name', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->textInput([
            'maxlength' => true,
            'placeholder'=>'Имя
        ']);

    echo $form->field($model, 'last_name', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->textInput([
            'maxlength' => true,
            'placeholder'=>'Фамилия'
        ]);

    echo $form->field($model, 'email', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->textInput([
            'maxlength' => true,
            'placeholder'=>'Email'
        ]);

    echo $form->field($model, 'password', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->passwordInput([
        'maxlength' => true,
        'placeholder'=>'Пароль'
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Зарегистрироватья', ['class' => 'btn btn-lg btn-primary btn-block']) ?>
    </div>

    <div class="hint-block">
        <p>Вы также можете зарегистрироваться на сайте,
            используя аккаунты соц. сетей.
            Для этого просто нажмите на соответствующую кнопку.</p>
    </div>

    <?php ActiveForm::end(); ?>

</div>
