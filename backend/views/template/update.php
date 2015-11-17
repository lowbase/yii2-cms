<?php

/* @var $this yii\web\View */
/* @var $model common\models\Template */
/* @var $searchModel backend\models\OptionSearch */
/* @var $dataProvider backend\models\OptionSearch */


$this->title = 'Редактирование шаблона';

?>

<div class="template-update">

    <?=  $this->render('@app/views/site/_alert') ?>

    <?= $this->render('_form', [
        'model' => $model,
       'searchModel' => $searchModel,
       'dataProvider' => $dataProvider,
    ]) ?>

</div>
