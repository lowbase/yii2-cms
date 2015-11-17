<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Вход на сайт';
?>
<div class="site-login">

    <?php
    if (Yii::$app->session->hasFlash('confirm-success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => 'Спасибо! Ваш Email успешно подтверждён. Можете авторизироваться'
        ]);
    }
    if (Yii::$app->session->hasFlash('newpass-success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => 'Новый пароль установлен.'
        ]);
    }
    if (Yii::$app->session->hasFlash('confirm-error')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-danger'
            ],
            'body' => 'Ошибка подтверждения Email.'
        ]);
    } ?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'login-form',
        'options' => ['class'=>'form-signin'],
    ]); ?>
    <?= $form->field($model, 'email', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->textInput(['placeholder'=>'Email']) ?>
    <?= $form->field($model, 'password', [
        'template'=>"{input}\n{hint}\n{error}"])
        ->passwordInput(['placeholder'=>'Пароль']) ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Войти', [
            'class' => 'btn btn-lg btn-primary btn-block',
            'name' => 'login-button'
        ]) ?>
    </div>

    <div class="hint-block">
        <p><?= Html::a('Зарегистрируйтесь', ['/registration']) ?> если у Вас еще нет аккаунта.</p>
        <p>Не помните пароль? Вы можете <?= Html::a('восстановить', ['/repass']) ?> его через электронную почту.</p>
    </div>

    <?php ActiveForm::end(); ?>

</div>
