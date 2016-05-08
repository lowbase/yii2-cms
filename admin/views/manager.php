<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use mihaildev\elfinder\ElFinder;
use yii\web\JsExpression;

$this->title = 'Файловый менеджер';

echo ElFinder::widget([
    'language'         => 'ru',
    'controller'       => 'elfinder',
    'filter'           => 'image',
    'callbackFunction' => new JsExpression('function(file, id){}'),
    'frameOptions' => ['style'=>"width: 100%; height: 500px; border: 0;"],
]);
?>
