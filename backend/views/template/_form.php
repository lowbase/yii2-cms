<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\elfinder\InputFile;
use common\models\Template;
use common\models\Option;
use kartik\grid\GridView;
use kartik\widgets\SwitchInput;

/* @var $searchModel backend\models\OptionSearch */
/* @var $dataProvider backend\models\OptionSearch */

$this->registerJs('
        var exfields = $(".ex-field-type");
        $.each(exfields, function(index, value){
            var val = $(this).val();
            if (val && val !== "0"){
                    $(this).parent().parent().parent().show();
                }
        });

        $(".ex").click(function(){
            var id = $(this).attr("id").substr(5);
            var exid = $("#ex-"+id);
            var display = exid.is(":visible");
            if (!display) {
                $("#ex-f-"+id).removeClass("label-default")
                .removeClass("on-ex")
                .addClass("label-success")
                .addClass("off-ex");
                exid.show();
            }
            else {
                 $("#ex-f-"+id).removeClass("label-success")
                .removeClass("off-ex")
                .addClass("label-default")
                .addClass("on-ex");
                exid.hide();
                exid.find("input").val("");
                exid.find(".bootstrap-switch input").bootstrapSwitch("state", false);
                exid.find(".ex-field-type").val(0);

            }
        });

        $(".delete-all").click(function(){
        var keys = $(".grid-view").yiiGridView("getSelectedRows");
        $.ajax({
            url: "/admin/option/multidelete",
            type:"POST",
            data:{keys: keys},
            success: function(data){
                location.reload();
            }
            });
        });
    ');
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-book"></i> <?= Html::a('Шаблоны', [
                    '/template'])?> → <?= ($model->isNewRecord) ? "Создание" : "Редактирование"?>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">

            <div class="form-group row">
                <div class="col-sm-12">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить', [
                        'class' => 'btn btn-primary']) ?>
                    <?php
                    if (!$model->isNewRecord) {
                        echo Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                            'delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'path')->widget(InputFile::className(), [
                        'language'      => 'ru',
                        'controller'    => 'front-elfinder',
                        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                        'options'       => ['class' => 'form-control'],
                        'buttonOptions' => ['class' => 'btn btn-default'],
                        'buttonName'    => 'Выбрать файл',
                        'multiple'      => false
                    ]);
                    ?>
                </div>
            </div>

        </div>
    </div>

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-th-list"></i> Расширенные «быстрые» поля для документов:
                <?php
                for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
                    $type_option = 'option_'.$i.'_type';
                    if ($model->$type_option) {
                        echo "<span class='pointer ex label label-success off-ex' id='ex-f-".$i."'>".$i."</span>&nbsp;";
                    } else {
                        echo "<span class='pointer ex label label-default on-ex' id='ex-f-".$i."'>".$i."</span>&nbsp;";
                    }
                }
                ?>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <p class="hint-block">
                Количество «быстрых» полей ограничено <?= Template::OPTIONS_COUNT?>.
                Нажмите на цифру для активации расширенного поля.
                При необходимости большего количества воспользуйтесь
                дополнительными полями <?= ($model->isNewRecord) ? ", которые будут
                 доступны после сохранения шаблона" : ""?>.
                Дополнительные поля также необходимы для организации
                аттрибутов со множественными значениями.
            </p>

            <?php
            for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {?>
                <div class="row hidden-block" id="ex-<?=$i?>">
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_type')->dropDownList([''] + Template::getTypesField(), [
                            'class'=>'ex-field-type form-control']) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_param')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'option_'.$i.'_require')->widget(SwitchInput::classname(), [
                            'pluginOptions' => [
                                'onText' => 'Да',
                                'offText' => 'Нет',
                            ]
                            ]); ?>
                    </div>
                </div>
            <?php
            }?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

        <?php
        if (!$model->isNewRecord) {
            $gridColumns = [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'contentOptions' => ['class' => 'kartik-sheet-style'],
                    'width' => '30px',
                    'header' => '',
                    'headerOptions' => ['class' => 'kartik-sheet-style']
                ],
                [
                    'width' => '70px',
                    'attribute' => 'id',
                ],
                'name',
                [
                    'attribute' => 'type',
                    'value' => function ($model) {
                        return Option::getTypesField()[$model->type];
                    },
                    'filterType' => yii\grid\GridView::FILTER_POS_HEADER,
                    'filter' => Option::getTypesField(),
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'filterInputOptions' => [
                        'placeholder' => ' ',
                        'class' => 'form-control'
                    ],
                    'format' => 'raw'
                ],
                'param',
                'require',
                [
                    'class' => 'kartik\grid\BooleanColumn',
                    'attribute' => 'multiple',
                    'vAlign' => 'middle'
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            $options = [
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ];
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                '/option/update', 'id' => $key], $options);
                        },
                       'delete' => function ($url, $model, $key) {
                           $options = [
                               'title' => Yii::t('yii', 'Delete'),
                               'aria-label' => Yii::t('yii', 'Delete'),
                               'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                               'data-method' => 'post'
                           ];
                           return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['/option/delete', 'id' => $key], $options);
                       }
                    ]
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
                    'type' => GridView::TYPE_PRIMARY,
                    'before' => Html::a('<span class="glyphicon glyphicon-plus"></span> Создать', [
                        '/option/create', 'template_id' => $model->id], ['class' => 'btn btn-success']),
                    'after' => "<div class='text-right'><b>Выбранные:</b> " .
                        Html::button('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                            'class' => 'btn btn-danger delete-all']) .
                        "</div>",
                    'heading' => '<h3 class="panel-title">
                                    <i class="glyphicon glyphicon-th-list"></i>
                                    Дополнительные поля документов</h3>',
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
        }
        ?>

</div>
