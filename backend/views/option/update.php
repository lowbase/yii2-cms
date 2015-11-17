<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Option */

$this->title = 'Редактирование опции';
?>
<div class="option-update">

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-th-list"></i> <?= Html::a('Шаблоны', ['/template'])?> →
                <?= Html::a($model->template->name, ['/template/update', 'id' => $model->template_id])?> →
                Редактирование дополнительного поля
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
