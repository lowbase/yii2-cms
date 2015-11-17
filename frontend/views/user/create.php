<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Регистрация';
?>
<div class="user-create">

    <?php
    if (Yii::$app->session->hasFlash('send-email')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => 'На Email отправлено письмо.
            Подтвердите ваш электронный адрес.'
        ]);
    } ?>

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
