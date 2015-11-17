<?php
use common\helpers\CFF;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Html;
use common\models\AuthItem;
use common\models\User;

/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider backend\models\UserSearch */

$this->title = 'Пользователи';

    $this->registerJs('
        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "/admin/user/multidelete",
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
            url: "/admin/user/multiopen",
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
            url: "/admin/user/multiclose",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');

?>

<div class="user-index">

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
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function () {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model) {
                return Yii::$app->controller->renderPartial('_view', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style']
        ],
        [
            'attribute' => 'id',
            'width' => '70px',
        ],
        [
            'attribute'=>'role_id',
            'value' => function ($model) {
                return (isset($model->role)) ? $model->role->name : '';
            },
            'filter' => AuthItem::getAll(1),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => [
                'placeholder' => '',
                'class' => 'form-control'],
            'format'=>'raw'
        ],
        [
            'attribute'=>'first_name',
            'format'=>'html',
            'value' => function ($model) {
                return Html::a($model->first_name, [
                    '/user/update', 'id' => $model->id
                ]);
            },
        ],
        'last_name',
        'email:email',
        'phone',
        [
            'attribute' => 'created_at',
            'value' => function ($model) {
                return CFF::FormatData($model->created_at, true);
            },
            'width'=>'200px',
            'filter' => DatePicker::widget([
                'value'=> isset($_GET['UserSearch']['created_at'])?$_GET['UserSearch']['created_at']:'',
                'name' => 'UserSearch[created_at]',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true
                ]
            ])
        ],
        [
            'attribute' => 'status',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($model) {
                switch ($model->status) {
                    case User::STATUS_BLOCKED:
                        return '<span class="label label-danger">
                        <i class="glyphicon glyphicon-lock"></i> Заблокирован</span>';
                        break;
                    case User::STATUS_WAIT:
                        return '<span class="label label-warning">
                        <i class="glyphicon glyphicon-hourglass"></i> Не активен</span>';
                        break;
                    case User::STATUS_ACTIVE:
                        return '<span class="label label-success">
                        <i class="glyphicon glyphicon-ok"></i> Активен</span>';
                        break;
                }
                return false;
            },
            'filter' => User::getStatusesArray()
        ],
        [
            'template' => '{update} {delete}',
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
                'heading' => '<i class="glyphicon glyphicon-user"></i> Пользователи',
                'type' => GridView::TYPE_PRIMARY,
                'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> Регистрация', '/registration', [
                    'class' => 'btn btn-success']),
                'after' => "<div class='text-right'><b>Выбранные:</b> ".
                    Html::button('<span class="glyphicon glyphicon-eye-open"></span> Активировать', [
                        'class' => 'btn btn-default open-all'])." ".
                    Html::button('<span class="glyphicon glyphicon-eye-close"></span> Заблокировать', [
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
