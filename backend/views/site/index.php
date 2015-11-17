<?php

/* @var $this yii\web\View */

$this->title = 'Рабочий стол';
?>
<div class="site-index">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><span class="glyphicon glyphicon-home"></span> Рабочий стол</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <p>Добро пожаловать в систему управления сайтом <a href="//lowbase.ru" title="lowbase">lowBase</a>.</p>
            <p>Главную страницу панели администрирования предлагаем оформить по собственному желанию, исходя из индивидуальных особенностей вашего сайта.</p>
            <ul>
                <li>Controller: <em><?= Yii::$app->controllerPath ?>/<?= ucfirst(Yii::$app->controller->id) ?>Controller</em></li>
                <li>Action: <em><?= Yii::$app->controller->action->id ?></em></li>
                <li>View: <em><?= Yii::$app->viewPath ?>/<?= Yii::$app->controller->id ?>/<?= Yii::$app->controller->action->id ?></em></li>
            </ul>
        </div>
    </div>
</div>
