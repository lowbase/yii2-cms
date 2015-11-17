<?php

/* @var $this yii\web\View */
/* @var $model common\models\Document */

$this->title = 'Редактирование документа';
?>

<div class="document-update">

    <?=  $this->render('@app/views/site/_alert') ?>

    <?= $this->render('_form', [
        'model' => $model,
        'template' => $template
    ]) ?>
</div>
