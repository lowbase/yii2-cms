<?php

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = 'Редактирование сообщения';
?>
<div class="message-update">

    <?=  $this->render('@app/views/site/_alert') ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
