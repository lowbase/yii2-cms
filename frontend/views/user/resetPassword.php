<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\ResetPasswordForm */

$this->title = 'Новый пароль';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">

    <?php
    if (Yii::$app->session->hasFlash('newpass-success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body'=>'Новый пароль установлен.'
        ]);
    } else {
    ?>
        <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form',
            'options' => ['class'=>'form-signin']]);
            echo $form->field($model, 'password', [
                'template'=>"{input}\n{hint}\n{error}"])
                ->passwordInput(['placeholder'=>'Пароль']) ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', [
                    'class' => 'btn btn-lg btn-primary btn-block'
                ]) ?>
            </div>

        <div class="hint-block">
            Придумайте и укажите новый пароль для
            дальнейшего входа на сайт.
        </div>

        <?php ActiveForm::end(); ?>
    <?php
    }?>
</div>
