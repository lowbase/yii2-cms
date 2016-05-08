<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

Modal::begin([
    'header' => '<h1 class="text-center">Восстановление пароля</h1>',
    'toggleButton' => false,
    'id' => 'pass'
]);

    $form = ActiveForm::begin([
        'id' => 'pass-form',
        'fieldConfig' => [
            'template' => "{input}\n{hint}\n{error}"
        ],
    ]);

    echo $form->field($model, 'email')->textInput([
        'maxlength' => true,
        'placeholder' => $model->getAttributeLabel('email')
    ]);

    echo $form->field($model, 'password')->passwordInput([
        'maxlength' => true,
        'placeholder' => $model->getAttributeLabel('password')
    ]);

    echo $form->field($model, 'captcha')->widget(Captcha::className(), [
        'captchaAction' => '/lowbase-user/default/captcha',
        'options' => [
            'class' => 'form-control',
            'placeholder' => $model->getAttributeLabel('captcha')
        ],
        'template' => '<div class="row">
                <div class="col-lg-8">{input}</div>
                <div class="col-lg-4">{image}</div>
                </div>',
    ]);
    ?>

    <p class="hint-block">
        Ссылка с активацией нового пароля будет отправлена на Email, указанный при регистрации.
    </p>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="glyphicon glyphicon-refresh"></i> Сбросить пароль', [
            'class' => 'text-center btn btn-lg btn-primary',
            'name' => 'pass-button'
        ]);
        ?>
    </div>

<?php
    ActiveForm::end();
Modal::end();

if (Yii::$app->session->hasFlash('success')|| $model->hasErrors()) {
    $this->registerJs('$("#pass").modal("show")');
}
