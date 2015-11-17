<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use mihaildev\elfinder\InputFile;

$this->title = 'Настройки сайта';

$this->registerJs('
        var exfields = $(".ex-field-type");
        $.each(exfields, function(index, value){
            var val = $(this).val();
            if (val){
                    $(this).parent().parent().parent().show();
                }
        });
        $(".activate-ex").click(function(){
            var id = $(this).attr("id").substr(9);
            var html = $(this).html();
            $(this).parent().html(html);
            $("#ex"+id).show();
        });
    ');
?>

<?=  $this->render('@app/views/site/_alert') ?>

<div class="setting-form">

    <div class="box box-panel">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="glyphicon glyphicon-cog"></i> Настройки сайта</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">

            <?php $form = ActiveForm::begin(['options' => [
                'class'=>'form',
            ]]); ?>

            <div class="form-group row">
                <div class="col-sm-12">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
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
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'logo')->widget(InputFile::className(), [
                        'language'      => 'ru',
                        'controller'    => 'elfinder',
                        'filter'        => 'image',
                        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                        'options'       => ['class' => 'form-control'],
                        'buttonName'    => 'Выбрать файл',
                        'buttonOptions' => ['class' => 'btn btn-default'],
                        'multiple'      => false
                    ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'favicon')->widget(InputFile::className(), [
                        'language'      => 'ru',
                        'controller'    => 'elfinder',
                        'filter'        => 'image',
                        'template'      => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                        'options'       => ['class' => 'form-control'],
                        'buttonName'    => 'Выбрать файл',
                        'buttonOptions' => ['class' => 'btn btn-default'],
                        'multiple'      => false
                    ]);
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'copyright')->textarea(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'counter')->textarea(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'message_options_names')->textInput() ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>