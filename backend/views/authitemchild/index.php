<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\AuthItem;

/* @var $searchModel backend\models\AuthItemChildSearch */
/* @var $dataProvider backend\models\AuthItemChildSearch */

$this->title = 'Допуски';

$this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
       $.ajax({
            url: "/admin/authitemchild/multidelete",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>
<div class="role-index">

    <?=  $this->render('@app/views/site/_alert') ?>

    <?php
    $gridColumns = [
        [
            'class' => 'kartik\grid\SerialColumn',
            'contentOptions' => ['class'=>'kartik-sheet-style'],
            'width' => '30px',
            'header' => '',
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        [
            'attribute' => 'parent',
            'value' => function ($model) {
                return $model->parent;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => AuthItem::getAll(1),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear'=>true],
            ],
            'filterInputOptions' => [
                'placeholder' => ' ',
                'class' => 'form-control'
            ],
            'format' => 'raw'
        ],
        [
            'attribute' => 'child',
            'value' => function ($model) {
                return Html::a($model->child, [
                    '/permission/update', 'id' => $model->id
                ]);
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => AuthItem::getAll(),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => ' ','class' => 'form-control'],
            'format'=>'raw'
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
            'template' => '{update} {delete}'
        ],
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'headerOptions'=>['class'=>'kartik-sheet-style'],
        ],
    ];

        echo GridView::widget([
            'layout' => "{items}\n{summary}\n{pager}",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'],
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'panel' => [
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-eye-close"></i> Допуски</h3>',
                'type' => GridView::TYPE_PRIMARY,
                'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> Создать', [
                    'create'], ['class' => 'btn btn-success']),
                'after' => "<div class='text-right'><b>Выбранные:</b> " .
                    Html::button('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                        'class' => 'btn btn-danger delete-all']) .
                    "</div>"
            ],
            'export' => [
                'fontAwesome' => true
            ],
            'bordered' => true,
            'striped' => true,
            'condensed' => true,
            'persistResize' => false,
            'hover' => true,
            'responsive' => true,
        ]);
    ?>

</div>

