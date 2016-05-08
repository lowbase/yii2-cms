<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\document\components\TreeWidget;
use lowbase\document\models\Document;
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

?>
<div class="content-wrapper">
    <section class="brc">
        <?=
        Breadcrumbs::widget(
            [
                'homeLink' => ['label' => 'Рабочий стол', 'url' => ['/admin/index']],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Версия</b> 1.0
    </div>
    <strong>&copy; <?=date('Y')?>, <a href="http://lowbase.ru">lowBase</a></strong>
</footer>

<aside class="control-sidebar control-sidebar-light">
    <div class="tab-content">
        <?= TreeWidget::widget(['data' => Document::find()->orderBy(['position' => SORT_ASC])->all()])?>
    </div>
</aside>

<div class='control-sidebar-bg'></div>