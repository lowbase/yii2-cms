<?php

/* @var $this yii\web\View */
/* @var $model common\models\Template */

$this->title = 'Создание шаблона';
?>

<div class="template-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
