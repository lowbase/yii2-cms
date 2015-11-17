<?php

use yii\helpers\Html;
use common\helpers\CFF;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use common\models\Message;
use common\models\Document;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сообщения';
?>

    <div class="message-index">

        <?=  $this->render('@app/views/site/_alert') ?>

        <div class="search-form">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

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
                'attribute' => 'content',
                'format' => 'raw',
                'value'=>function ($model) {
                    if ($model->content) {
                        return CFF::shortString($model->content, 200);
                    } else {
                        return null;
                    }
                },
            ],
            [
                'attribute' => 'attachment',
                'format' => 'raw',
                'value'=>function ($model) {
                    if ($model->attachment) {
                        return CFF::shortString($model->attachment, 200);
                    } else {
                        return null;
                    }
                },
            ],
            [
                'attribute' => 'for_document_id',
                'format' => 'raw',
                'value'=>function ($model) {
                    if ($model->for_document_id) {
                        $return = isset($model->for_document_id) ? Html::a($model->forDocument->name, [
                            '/document/update', 'id' => $model->for_document_id]) : "";
                        $return .= " (" . $model->for_document_id . ")";
                        return $return;
                    } else {
                        return null;
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => Document::getAll(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
                'filterInputOptions' => [
                    'placeholder' => ' ',
                    'class' => 'form-control'
                ],
            ],
            [
                'attribute' => 'for_user_id',
                'value'=>function ($model) {
                    $return = '';
                    if ($model->for_user_id) {
                        if (isset($model->forUser)) {
                            $user = $model->forUser;
                            $return = $user->first_name;
                            if ($user->last_name) {
                                $return .= " " . $user->last_name;
                            }
                        }
                        $return .= " (" . $model->for_user_id . ")";
                        return $return;

                    } else {
                        return null;
                    }
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => User::getAll(),
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
                'attribute' => 'parent_message_id',
                'width' => '70px',
            ],
            [
                'attribute'=>'created_at',
                'value' => function ($model) {
                    return CFF::FormatData($model->created_at, true);
                },
                'width'=>'200px',
                'filter' => DatePicker::widget([
                    'value'=> isset($_GET['DocumentSearch']['created_at']) ? $_GET['DocumentSearch']['created_at'] : null,
                    'name' => 'DocumentSearch[created_at]',
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true
                    ]
                ])
            ],
            [
                'attribute'=>'created_user_name',
                'format'=>'html',
                'value' => function ($model) {
                    if ($model->created_user_name) {
                        return $model->created_user_name." <span class='node-id'>(".$model->created_user_id.")</span>";
                    } else {
                        return null;
                    }
                },
            ],
            [
                'attribute' => 'status',
                'vAlign' => 'middle',
                'format' => 'raw',
                'value' => function ($model) {
                    switch ($model->status) {
                        case Message::STATUS_BLOCKED:
                            return '<span class="label label-danger">
                        <i class="glyphicon glyphicon-lock"></i> ' .
                            Message::getStatuses()[$model->status] . '</span>';
                            break;
                        case Message::STATUS_ACTIVE:
                            return '<span class="label label-success">
                        <i class="glyphicon glyphicon-ok"></i> ' .
                            Message::getStatuses()[$model->status] . '</span>';
                            break;
                        case Message::STATUS_VISITED:
                            return '<span class="label label-warning">
                        <i class="glyphicon glyphicon-eye-open"></i> ' .
                            Message::getStatuses()[$model->status] . '</span>';
                            break;
                    }
                    return false;
                },
                'filter' => Message::getStatuses()
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
            [
                'class'=>'kartik\grid\CheckboxColumn',
                'headerOptions'=>['class'=>'kartik-sheet-style'],
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
                'heading' => '<h3 class="panel-title">
                                <i class="glyphicon glyphicon-comment"></i> Сообщения
                              </h3>',
                'type' => GridView::TYPE_PRIMARY,
                'before'=>
                    Html::a('<span class="glyphicon glyphicon-send"></span> Написать', [
                        'create'], ['class' => 'btn btn-success']),
                'after'=>"<div class='text-right'><b>Выбранные:</b> ".
                    Html::button('<span class="glyphicon glyphicon-eye-open"></span> Опубликовать', [
                        'class' => 'btn btn-default open-all'])." ".
                    Html::button('<span class="glyphicon glyphicon-eye-close"></span> Скрыть', [
                        'class' => 'btn btn-default close-all'])." ".
                    Html::button('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                        'class' => 'btn btn-danger delete-all']).
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

<?php
$this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "/admin/message/multidelete",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
        $(".open-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "/admin/message/multipublicate",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
        $(".close-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "/admin/message/multiclose",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>