<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->context->layout='simple';
?>
<div class="site-error inverce">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-home"></i> На сайт', '/', ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-cog"></i> В панель администрирования', ['/'], ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-user"></i> Авторизация', ['logout'], ['class' => 'btn btn-default']) ?>
    </p>

</div>
