<?php

use yii\helpers\Html;
use common\models\Template;

/* @var $this yii\web\View */
/* @var $model common\models\Option */

$this->title = 'Создание опции';

?>
<div class="option-create">

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-th-list"></i> <?= Html::a('Шаблоны', ['/template'])?> →
                <?php
                if ($model->template_id) {
                    $template = Template::findOne($model->template_id);
                    /** @var \common\models\Template $template  */
                    if ($template) {
                        echo Html::a($template->name, ['/template/update', 'id' => $template->id]) ." →";
                    }
                }
                ?>
                Создание дополнительного поля
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
