<?php
use yii\helpers\Html;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use mihaildev\elfinder\InputFile;
use kartik\widgets\ActiveForm;
use common\models\Template;
use common\models\Document;
use yii\bootstrap\ButtonDropdown;
use kartik\widgets\Select2;

?>
<div class="document-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'document',
        'enableClientValidation' => false,
        'method' => 'POST',
        'options' => [
            'enctype'=>'multipart/form-data'
        ]
    ]);
    ?>

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="glyphicon glyphicon-file"></i> <?= Html::a('Документы', [
                '/document'])?> → <?= ($model->isNewRecord) ? "Создание" : "Редактирование"?>
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
                        echo Html::a('<span class="glyphicon glyphicon-eye-open"></span> Просмотр', [
                                'view', 'id' => $model->id ], [
                                'class' => 'btn btn-default']) . " ";
                        echo Html::a('<span class="glyphicon glyphicon-level-up"></span> Создать дочерний', [
                                'create', 'parent_id' => $model->id ], [
                                'class' => 'btn btn-default']) . " ";
                        echo Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', [
                            'delete', 'id' => $model->id, 'from_home' => true], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Действительно хотите удалить документ?',
                                'method' => 'post',
                            ]
                            ]);
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    if ($model->isNewRecord) {
                        echo $form->field($model, 'id')->textInput([
                            'maxlength' => true,
                            'disabled' =>true,
                            'placeholder'=>'Новый'
                        ]);
                    } else {
                        echo $form->field($model, 'id')->textInput([
                            'maxlength' => true,
                            'disabled' =>true
                        ]);
                    }
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'title', [
                        'addon' => [
                            'append' => [
                                'content'=> Html::a('Повторить название', '#', [
                                    'class' =>['btn btn-default repeat-name']]),
                                'asButton'=>true,
                            ],
                            'groupOptions' => [
                                'id' => 'title-btn'
                            ]
                        ]
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'alias', [
                        'addon' => [
                            'append' => [
                                'content' => ButtonDropdown::widget([
                                    'label' => 'Сформировать',
                                    'dropdown' => [
                                        'items' => [
                                            ['label' => 'Из названия', 'url' => '#', 'options' => ['class'=>'translate-name']],
                                            ['label' => 'Из заголовка', 'url' => '#', 'options' => ['class'=>'translate-title']],
                                        ],
                                    ],
                                    'options' => ['class'=>'btn-default']
                                ]),
                                'asButton' => true
                            ],
                            'groupOptions' => [
                                'id' => 'aliast-btn'
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'data' => Document::getAll(),
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'template_id')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'data' => Template::getAll(),
                        'options' => [
                            'placeholder' => '',
                            'class' => 'template_id'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'meta_description')->textarea(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'meta_keywords')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'annotation')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                        'editorOptions' => ElFinder::ckeditorOptions('elfinder', []),
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    if ($model->img) {
                        echo "<img src='".$model->img."' class='doc_img'>";
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'img')->widget(InputFile::className(), [
                        'language'      => 'ru',
                        'controller'    => 'elfinder',
                        'filter'        => 'image',
                        'template'      => '<div class="input-group">
                                                {input}<span class="input-group-btn">{button}</span>
                                            </div>',
                        'options'       => ['class' => 'form-control'],
                        'buttonName'    => 'Выбрать файл',
                        'buttonOptions' => ['class' => 'btn btn-default'],
                        'multiple'      => false
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'status')->dropDownList(Document::getStatuses()); ?>
                </div>
            </div>
        </div>
    </div>

    <div id="options">
        <?php echo $this->render('_options_fields', ['model' => $model, 'template' => $template]);?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$document_id = ($model->isNewRecord) ? 0 : $model->id;
$this->registerJs("
    $('.repeat-name').click(function(){
    var text = $('#document-name').val();
    $('#document-title').val(text);
    });
    $('.translate-name').click(function(){
    var text = $('#document-name').val().toLowerCase();
    result = translit(text);
    $('#document-alias').val(result);
    });
    $('.translate-title').click(function(){
    var text = $('#document-title').val().toLowerCase();
    result = translit(text);
    $('#document-alias').val(result);
    });

    $('#document-template_id').change(function(){
        var template_id = $(this).val();
        $.ajax({
            url: '/admin/document/ajaxoptions',
            type: 'POST',
            data: {
                'id' : " . $document_id . ",
                'template_id' : template_id
            },
            success: function(data){
                $('#options').html(data);
            }
        });
});
");
?>
