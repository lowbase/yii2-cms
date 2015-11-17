<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\PasswordResetRequestForm */

$this->title = 'Забыли пароль?';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">

    <?php
    if (Yii::$app->session->hasFlash('repass-success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body'=>'На Email выслано письмо
            с дальнейшими инструкциями.'
        ]);
    }
    if (Yii::$app->session->hasFlash('repass-error')) {
        echo Alert::widget([
            'options' => [
                'success' => 'alert-danger'
            ],
            'body'=>'Ошибка восстановления пароля.
             Обратитесь к администратору.'
        ]);
    } ?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
            <?php
            $form = ActiveForm::begin([
                'id' => 'request-password-reset-form',
                'options' => ['class'=>'form-signin']
                ]);
            ?>
            <?= $form->field($model, 'email', [
                'template'=>"{input}\n{hint}\n{error}"])
                ->textInput(['placeholder'=>'Email']) ?>
            <div class="form-group">
                <?= Html::submitButton('Восстановить', ['class' => 'btn btn-lg btn-primary btn-block']) ?>
            </div>
            <div class="hint-block">
                Укажите Email, который вы указывали при
                регистрации или в личном кабинете.
                На него будет отправлена ссылка для сброса пароля.
            </div>
    <?php ActiveForm::end(); ?>
</div>
