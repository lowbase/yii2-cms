<?php
/* @var $this yii\web\View */
/* @var $model common\models\Document */

$this->title = 'Новый документ';
?>
<div class="document-create">

    <?= $this->render('_form', [
        'model' => $model,
        'template' => $template
    ]) ?>

</div>
