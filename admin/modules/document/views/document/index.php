<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
 
use lowbase\document\models\Document;
use lowbase\document\models\Template;
use lowbase\user\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\date\DatePicker;
use lowbase\document\DocumentAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\document\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('document', 'Менеджер документов');
$this->params['breadcrumbs'][] = $this->title;
DocumentAsset::register($this);
?>

<div class="document-index">

    <?= $this->render('_search', ['model' => $searchModel]);?>

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
                'format' => 'raw',
                'value' => function ($model) {
                    $icon = ($model->is_folder) ? 'glyphicon glyphicon-folder-open' : 'glyphicon glyphicon-file';
                    return "<span class='".$icon."'></span> " . $model->id;
                },
            ],
            'name',
            // 'alias',
            // 'title',
            // 'meta_keywords:ntext',
            // 'meta_description:ntext',
            // 'annotation:ntext',
            // 'content:ntext',
            // 'image',
            [
                'attribute' => 'parent_id',
                'vAlign' => 'middle',
                'format' => 'raw',
                'width' => '150px',
                'value' => function ($model) {
                    return ($model->parent_id && $model->parent) ? $model->parent->name : null;
                },
                'filter' => Document::getAll(),
                'filterType'=> GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
                'filterInputOptions' => [
                    'placeholder' => ' ',
                    'class' => 'form-control'
                ],
            ],
            [
                'attribute' => 'template_id',
                'vAlign' => 'middle',
                'format' => 'raw',
                'width' => '150px',
                'value' => function ($model) {
                    return ($model->template_id && $model->template) ? $model->template->name : null;
                },
                'filter' => Template::getAll(),
                'filterType'=> GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
                'filterInputOptions' => [
                    'placeholder' => ' ',
                    'class' => 'form-control',
                ],
            ],
            // 'is_folder',
            [
                'attribute' => 'created_at',
                'vAlign' => 'middle',
                'format' =>  ['date', 'dd.MM.Y HH:mm:ss'],
                'width'=>'200px',
                'filter' => DatePicker::widget([
                    'value'=> isset($_GET['DocumentSearch']['created_at'])?$_GET['DocumentSearch']['created_at']:'',
                    'name' => 'DocumentSearch[created_at]',
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true
                    ]
                ])
            ],
            // 'updated_at',
            [
                'attribute' => 'created_by',
                'vAlign' => 'middle',
                'format' => 'raw',
                'width' => '200px;',
                'value' => function ($model) {
                    $value = '';
                    if (isset($model->created)) {
                        $value .= $model->created->first_name;
                        if ($model->created->last_name) {
                            $value .= ' ' . $model->created->last_name;
                        }
                    }
                    return $value . ' (' . $model->created_by . ')';
                },
                'filter' => User::getAll(true),
                'filterType'=> GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear'=>true],
                ],
                'filterInputOptions' => [
                    'placeholder' => ' ',
                    'class' => 'form-control',
                    'id' => 'created_by'
                ],
            ],
            [
                'attribute' => 'status',
                'vAlign' => 'middle',
                'format' => 'raw',
                'value' => function ($model) {
                    switch ($model->status) {
                        case Document::STATUS_BLOCKED;
                            return '<span class="label label-danger">
                            <i class="glyphicon glyphicon-lock"></i> '.Document::getStatusArray()[Document::STATUS_BLOCKED].'</span>';
                            break;
                        case Document::STATUS_WAIT:
                            return '<span class="label label-warning">
                            <i class="glyphicon glyphicon-hourglass"></i> '.Document::getStatusArray()[Document::STATUS_WAIT].'</span>';
                            break;
                        case Document::STATUS_ACTIVE:
                            return '<span class="label label-success">
                            <i class="glyphicon glyphicon-ok"></i> '.Document::getStatusArray()[Document::STATUS_ACTIVE].'</span>';
                            break;
                    }
                    return false;
                },
                'filter' => Document::getStatusArray(),
            ],
            // 'updated_by',
            [
                'template' => '{view} {update} {delete}',
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
            'containerOptions' => ['style'=>'overflow: auto'],
            'headerRowOptions' => ['class'=>'kartik-sheet-style'],
            'filterRowOptions' => ['class'=>'kartik-sheet-style'],
            'pjax' => false,
            'panel' => [
                'heading' => '<i class="glyphicon glyphicon-book"></i> '.Yii::t('document', Yii::t('document', 'Документы')),
                'type' => GridView::TYPE_PRIMARY,
                'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('document', 'Добавить'), [
                    'document/create'], ['class' => 'btn btn-success']). ' '.
                    Html::button('<span class="glyphicon glyphicon-search"></span> '.Yii::t('document', 'Поиск'), [
                        'class' => 'filter btn btn-default',
                        'data-toggle' => 'modal',
                        'data-target' => '#filter',
                    ])
                ,
                'after' => "<div class='text-right'><b>".Yii::t('document', 'Выбранные').":</b> ".
                    Html::button('<span class="glyphicon glyphicon-eye-open"></span> '.Yii::t('document', 'Опубликовать'), [
                        'class' => 'btn btn-default open-all'])." ".
                    Html::button('<span class="glyphicon glyphicon-eye-close"></span> '.Yii::t('document', 'Скрыть'), [
                        'class' => 'btn btn-default close-all'])." ".
                    Html::button('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('document', 'Удалить'), [
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
                url: "' . Url::to(['document/multidelete']) . '",
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
                url: "' . Url::to(['document/multiactive']) . '",
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
                url: "' . Url::to(['document/multiblock']) . '",
                type:"POST",
                data:{keys: keys},
                success: function(data){
                    location.reload();
                }
                });
            });
        ');
    ?>
