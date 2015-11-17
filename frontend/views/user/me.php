<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = 'Личный кабинет';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Личный кабинет';
?>
<div class="user-update">

    <?php
    if (Yii::$app->session->hasFlash('me-update-success')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-success'
            ],
            'body' => 'Данные обновлены.'
        ]);
    }
    if (Yii::$app->session->hasFlash('eauth-fail')) {
        echo Alert::widget([
            'options' => [
                'class' => 'alert-danger'
            ],
            'body' => 'Невозможно подключить аккаунт.
            Он уже закреплен за другим профилем.'
        ]);
    } ?>

    <?= $this->render('_myform', ['model' => $model]) ?>

</div>
