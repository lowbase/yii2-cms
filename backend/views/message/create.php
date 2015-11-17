<?php

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = 'Новое сообщение';
?>
<div class="message-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
