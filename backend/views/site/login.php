<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Администрирование';
?>
<div class="site-login inverce">

    <p class="text-center">
        <?= Html::img('/admin/css/image/lowbase-bg.png') ?>
    </p>

    <?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
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

    <p class="hint-block">
        Вы можете войти используя аккаунты
        социальных сетей или <?= Html::a('вернуться на сайт', '/') ?>.
    </p>

    <div class="social social_log">
        <?= Html::a('', '/login/vkontakte', [
            'title' => 'Войти с помощью Вконтакте',
            'class' => 'vk'
        ]) ?>
        <?= Html::a('', '/login/odnoklassniki', [
            'title' => 'Войти с помощью Одноклассники',
            'class' => 'ok'
        ]) ?>
        <?= Html::a('', '/login/facebook', [
            'title' => 'Войти с помощью Facebook',
            'class' => 'fb'
        ]) ?>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
