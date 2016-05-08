<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\UserAsset;
use yii\helpers\Html;
$userAsset = UserAsset::register($this);

/* @var $this \yii\web\View */
/* @var $content string */

    $me = Yii::$app->user->identity;

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">
        <?php
            echo $this->render('header.php', [
                    'directoryAsset' => $directoryAsset,
                    'userAsset' => $userAsset,
                    'me' => $me
                ]
            );
            echo $this->render('left.php', [
                    'directoryAsset' => $directoryAsset,
                ]
            );
            echo $this->render('content.php', [
                    'content' => $content,
                    'directoryAsset' => $directoryAsset
                ]
            );
        ?>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
