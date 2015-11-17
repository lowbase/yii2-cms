<?php

use yii\helpers\Html;

$this->title = 'Новая роль / точка доступа';
?>

<div class="auth-item-create">

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-tower"></i> <?= Html::a('Роли и точки доступа', ['/role'])?> → Создание
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
