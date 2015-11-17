<?php

use yii\helpers\Html;
use common\helpers\CFF;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FieldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поиск по полям';
?>
<div class="field-index">

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
        'attribute' => 'option_id',
        'format' => 'raw',
        'value'=>function ($model) {
            $return = isset($model->option) ? Html::a($model->option->name, [
                '/document/update', 'id' => $model->document_id, '#' => 'field-' . $model->id]) : "";
            $return .= " (" . $model->option_id . ")";
            return $return;
        },
    ],
    [
        'attribute' => 'document_id',
        'format' => 'raw',
        'value'=>function ($model) {
            $return = isset($model->document) ? Html::a($model->document->name, [
                '/document/update', 'id' => $model->document_id]) : "";
            $return .= " (" . $model->document_id . ")";
            return $return;
        },
    ],
    'position',
    [
        'attribute' => 'value',
        'format' => 'raw',
        'value'=>function ($model) {
            return CFF::shortString($model->value, 200);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{update}',
        'buttons' => [
            'update' => function ($url, $model, $key) {
                $options = [
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                ];
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                    '/document/update', 'id' => $model->document_id, '#' => 'field-' . $key], $options);
            }
        ]
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
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Поиск по полям</h3>',
        'type' => GridView::TYPE_PRIMARY,
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
