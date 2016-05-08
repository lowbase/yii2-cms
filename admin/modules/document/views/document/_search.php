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
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\document\models\DocumentSearch */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
    'header' => '<h1 class="text-center">'.Yii::t('document', 'Поиск по параметрам').'</h1>',
    'toggleButton' => false,
    'id' => 'filter',
    'options' => [
        'tabindex' => false
    ],
]);
?>

<div class="document-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'id_from') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'id_till') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'alias') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'title') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'meta_keywords') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'meta_description') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'annotation') ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'content') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'status')->dropDownList([''=>''] + Document::getStatusArray()) ?>
        </div>
        <div class="col-lg-6">
            <?php  echo $form->field($model, 'is_folder')->dropDownList([''=>''] +[1 => Yii::t('document', 'Папка'), 2 => Yii::t('document', 'Документ')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'parent_id')->widget(Select2::classname(), [
                'data' => Document::getAll(),
                'options' => [
                    'placeholder' => '',
                    'id' => 'parent_id'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'position_from') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'position_till') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'template_id')->widget(Select2::classname(), [
                'data' => Template::getAll(),
                'options' => [
                    'placeholder' => '',
                    'id' => 'template_id'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'image') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'created_by')->widget(Select2::classname(), [
                'data' => User::getAll(true),
                'options' => [
                    'placeholder' => '',
                    'id' => 'created'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'updated_by')->widget(Select2::classname(), [
                'data' => User::getAll(true),
                'options' => [
                    'placeholder' => '',
                    'id' => 'updated'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'created_at_from')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]
                ) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'created_at_till')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]
                )  ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'updated_at_from')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]
                ) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'updated_at_till')
                ->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => ''],
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd.mm.yyyy'
                        ]
                    ]
                )  ?>
        </div>
    </div>

    <div class="form-group row text-center">
        <div class="col-lg-12">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> '.Yii::t('document','Найти'), ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<?php Modal::end(); ?>
