<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Document */

$this->title = $model->title;

?>
<div class="document-view">

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">
                <span class="glyphicon glyphicon-file"></span> Просмотр документа
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <h1><?= Html::decode($model->title)?></h1>
            <?= Html::decode($model->content)?>
        </div>
    </div>
</div>
