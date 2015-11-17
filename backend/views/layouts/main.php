<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use common\helpers\CFF;
use common\models\Setting;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$setting = Setting::find()->one();
?>

<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?=$setting->favicon?>"/>
    <?php $this->head() ?>
</head>
<?php $alias = CFF::GetAlias(Yii::$app->request->pathInfo);?>
<body class="hold-transition skin-blue sidebar-mini<?= ($alias[0] == '' || ($alias[0] == 'document' && (isset($alias[1]) && in_array($alias[1], ['create', 'update'])))) ? " control-sidebar-open" : ""?>">
<?php $this->beginBody() ?>
<div class="wrapper">
    <header class="main-header">
        <a href="/admin/" class="logo">
            <span class="logo-mini"><?= Html::img('/admin/css/image/lb-inverse.png')?></span>
            <span class="logo-lg"><?= Html::img('/admin/css/image/lowbase-inverse.png')?></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <i class="glyphicon glyphicon-menu-hamburger"></i>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="user user-menu">
                        <?php
                        $identity = Yii::$app->getUser()->getIdentity();
                        echo Html::beginTag('a', [
                            'href' => '/me',
                            'title' => 'Личный кабинет'
                        ]);
                        $options = [
                            'alt' => 'Аватар',
                            'title' => 'Аватар',
                            'class' => 'user-image'
                        ];
                        if (isset($identity->photo) && $identity->photo) {
                            echo Html::img('/' . CFF::getThumb($identity->photo), $options);
                        } else {
                            echo "<i class='glyphicon glyphicon-user'></i>";
                        }
                        echo "<span class='hidden-xs'>Юрий Шеховцов</span>";
                        echo Html::endTag('a');
                        ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="glyphicon glyphicon-tree-conifer"></i> <span class="hidden-xs"> Документы</span>',
                            '#', [
                                'data-toggle' => 'control-sidebar',
                                'title' => 'Документы'
                            ]);?>
                    </li>
                    <li>
                        <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> <span class="hidden-xs"> На сайт</span>',
                            '/', [
                                'title' => 'На сайт'
                            ]);?>
                    </li>
                    <li>
                        <?= Html::a('<i class="glyphicon glyphicon-log-out"></i> <span class="hidden-xs"> Выйти</span>',
                            ['/logout'], [
                            'title' => 'Выйти'
                        ]);?>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li <?= ($alias[0] == '' || ($alias[0] == 'document' && (isset($alias[1]) && in_array($alias[1], ['create', 'update'])))) ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-home"></i> <span>Рабочий стол</span>',
                        ['/'], [
                            'title' => 'Главная'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'document' && (!isset($alias[1]) || $alias[1] == 'index')) ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-file"></i> <span>Поиск по документам</span>',
                        ['/document'], [
                            'title' => 'Поиск по документам'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'field') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-th-list"></i> <span>Поиск по полям</span>',
                        ['/field'], [
                            'title' => 'Поиск по полям'
                        ]);?>
                </li>
                <li<?= (in_array($alias[0], ['template', 'option'])) ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-book"></i> <span>Шаблоны и поля</span>',
                        ['/template'], [
                            'title' => 'Шаблоны и поля'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'message') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-comment"></i> <span>Сообщения</span>',
                        ['/message'], [
                            'title' => 'Сообщения'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'user') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-user"></i> <span>Пользователи</span>',
                        ['/user'], [
                            'title' => 'Пользователи'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'role') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-tower"></i> <span>Роли и точки доступа</span>',
                        ['/role'], [
                            'title' => 'Роли и точки доступа'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'permission') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-eye-close"></i> <span>Допуски</span>',
                        ['/permission'], [
                            'title' => 'Допуски'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'manager') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-hdd"></i> <span>Менеджер файлов</span>',
                        ['/manager'], [
                            'title' => 'Файловый менеджер'
                        ]);?>
                </li>
                <li<?= ($alias[0] == 'setting') ? " class='active'" : ""?>>
                    <?= Html::a('<i class="glyphicon glyphicon-cog"></i> <span>Настройки</span>',
                        ['/setting'], [
                            'title' => 'Настройки'
                        ]);?>
                </li>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content">
            <?= $content ?>
        </section>
    </div>

    <footer class="main-footer no-print">
        <div class="pull-right hidden-xs">
            Версия 1.0
        </div>
        &copy; 2015 <?= Html::a('lowbase', '//lowbase.ru');?>. Все права защищены.
    </footer>

    <aside class="control-sidebar control-sidebar-light">
            <div><input id="jstree_search_input" class="form-control" placeholder="Поиск по названию или ID"></div>
            <div><span class="glyphicon glyphicon-cloud-upload cloud"></span></div>
            <div id="jstree_div">
                <?= $this->render('_tree'); ?>
            </div>
    </aside>

    <div class="control-sidebar-bg"></div>

</div>
<?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>
