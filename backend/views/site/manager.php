<?php
use mihaildev\elfinder\ElFinder;
use yii\web\JsExpression;

$this->title = 'Файловый менеджер';
?>

<div class="box box-panel">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="glyphicon glyphicon-hdd"></i> Менеджер файлов</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="glyphicon glyphicon-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?php
        echo ElFinder::widget([
            'language'         => 'ru',
            'controller'       => 'elfinder',
            'filter'           => 'image',
            'callbackFunction' => new JsExpression('function(file, id){}'),
            'frameOptions' => ['style'=>"width: 100%; height: 500px; border: 0;"],
        ]);
        ?>
    </div>
</div>
