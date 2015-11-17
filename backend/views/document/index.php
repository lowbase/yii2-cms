<?php
use common\helpers\CFF;
use common\models\Document;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Поиск по документам';
?>
<div class="document-index">

    <?=  $this->render('@app/views/site/_alert') ?>

    <div class="search-form">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>

    <?php
    $gridColumns = [
        [
            'class'=>'kartik\grid\SerialColumn',
            'contentOptions'=>['class'=>'kartik-sheet-style'],
            'width'=>'30px',
            'header'=>'',
            'headerOptions'=>['class'=>'kartik-sheet-style']
        ],
        [
            'vAlign' => 'middle',
            'attribute'=>'id',
            'format'=>'html',
            'value' => function ($model) {
                if ($model->is_folder) {
                    if ($model->status) {
                        return Html::img(['/css/image/icon-folder.png']) . " " . $model->id;
                    } else {
                        return Html::img(['/css/image/icon-folder-disable.png']) . " ". $model->id;
                    }
                } else {
                    if ($model->status) {
                        return Html::img(['/css/image/icon-file.png']) . " " . $model->id;
                    } else {
                        return Html::img(['/css/image/icon-file-disable.png']) ." " . $model->id;
                    }
                }
            },
        ],
        [
            'attribute'=>'name',
            'format'=>'html',
            'value' => function ($model) {
                if ($model->parent_name) {
                    return Html::a($model->name, [
                        '/document/update', 'id' => $model->id
                    ]);
                } else {
                    return null;
                }
            },
        ],
        [
            'attribute'=>'parent_name',
            'format'=>'html',
            'value' => function ($model) {
                if ($model->parent_name) {
                    return Html::a($model->parent_name, [
                        '/document/update', 'id' => $model->parent_id
                    ]) . " <span class='node-id'>(".$model->parent_id.")</span>";
                } else {
                    return null;
                }
            },
        ],
        [
            'attribute'=>'root_name',
            'format'=>'html',
            'value' => function ($model) {
                if ($model->root_name) {
                    return Html::a($model->root_name, [
                        '/document/update', 'id' => $model->root_id
                    ]) . " <span class='node-id'>(".$model->root_id.")</span>";
                } else {
                    return null;
                }
            },
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
            'attribute'=>'updated_at',
            'value' => function ($model) {
                return CFF::FormatData($model->updated_at, true);
            },
            'width'=>'200px',
            'filter' => DatePicker::widget([
                'value'=> isset($_GET['DocumentSearch']['updated_at'])?$_GET['DocumentSearch']['updated_at']:'',
                'name' => 'DocumentSearch[updated_at]',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true
                ]
            ])
        ],
        [
            'attribute'=>'updated_user_name',
            'format'=>'html',
            'value' => function ($model) {
                if ($model->updated_user_name) {
                    return $model->updated_user_name." <span class='node-id'>(".$model->updated_user_id.")</span>";
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
                    case Document::STATUS_BLOCKED:
                        return '<span class="label label-danger">
                        <i class="glyphicon glyphicon-lock"></i> ' .
                        Document::getStatuses()[$model->status] . '</span>';
                        break;
                    case Document::STATUS_ACTIVE:
                        return '<span class="label label-success">
                        <i class="glyphicon glyphicon-ok"></i> ' .
                        Document::getStatuses()[$model->status] . '</span>';
                        break;
                    case Document::STATUS_WITHOUT_NAV:
                        return '<span class="label label-primary">
                        <i class="glyphicon glyphicon-ok"></i> ' .
                        Document::getStatuses()[$model->status] . '</span>';
                        break;
                    case Document::STATUS_ONLY_NAV:
                        return '<span class="label label-warning">
                        <i class="glyphicon glyphicon-ok"></i> ' .
                        Document::getStatuses()[$model->status] . '</span>';
                        break;
                }
                return false;
            },
            'filter' => Document::getStatuses()
        ],
        [
            'class'=>'kartik\grid\ActionColumn',
        ],
        [
            'class'=>'kartik\grid\CheckboxColumn',
            'headerOptions'=>['class'=>'kartik-sheet-style'],
        ],
    ];

    echo GridView::widget([
        'layout'=>"{items}\n{summary}\n{pager}",
        'dataProvider'=> $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm'],
        'containerOptions' => ['style'=>'overflow: auto'],
        'headerRowOptions'=>['class'=>'kartik-sheet-style'],
        'filterRowOptions'=>['class'=>'kartik-sheet-style'],
        'pjax' => false,
        'panel'=>[
            'heading'=>'<h3 class="panel-title">
                            <span class="glyphicon glyphicon-file"></span> Поиск по документам
                        </h3>',
            'type'=>GridView::TYPE_PRIMARY,
            'before'=>
                Html::a('<span class="glyphicon glyphicon-plus"></span> Создать', [
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
            url: "/admin/document/multidelete",
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
            url: "/admin/document/multipublicate",
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
            url: "/admin/document/multiclose",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>