<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItemChild */

$this->title = 'Новый допуск';
?>
<div class="auth-item-child-create">

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-eye-close"></i> <?= Html::a('Допуски', ['/permission'])?> → Создание
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

        </div>
    </div>

</div>
