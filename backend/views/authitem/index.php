<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\AuthItem;

/* @var $searchModel backend\models\AuthItemSearch */
/* @var $dataProvider backend\models\AuthItemSearch */

$this->title = 'Роли и точки доступа';

$this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
       $.ajax({
            url: "/admin/authitem/multidelete",
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
            'attribute' => 'id',
            'width' => '70px',
        ],
        [
            'attribute'=>'name',
            'format'=>'html',
            'value' => function ($model) {
                return Html::a($model->name, [
                    '/role/update', 'id' => $model->id
                ]);
            },
        ],
        'description:ntext',
        [
            'attribute'=>'type',
            'value'=>function ($model) {
                return AuthItem::getTypes()[$model->type];
            },
            'filterType'=>yii\grid\GridView::FILTER_POS_HEADER,
            'filter' => AuthItem::getTypes(),
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
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{update} {delete}'
        ],
        [
            'class' => 'kartik\grid\CheckboxColumn',
            'headerOptions' => ['class' => 'kartik-sheet-style'],
        ]
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
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-tower"></i> Роли и точки доступа</h3>',
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

