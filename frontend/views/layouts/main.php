<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\helpers\CFF;
use common\models\Setting;

AppAsset::register($this);
?>

<?php
$this->beginPage();
$setting = Setting::find()->one();
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="shortcut icon" href="<?=$setting->favicon?>"/>
    </head>

    <body>
    <?php $this->beginBody() ?>
        <div class="wrap">
            <div class="container">
<!--                Шапка сайта-->
                <div class="header">
                    <div class="row">
                        <div class="col-sm-4 login">
                            <?php
                            if (Yii::$app->user->isGuest) { ?>
                                <div class="social">
                                    <?= Html::a('Войти', ['/login'], [
                                        'class' => 'enter link',
                                        'title' => 'Войти'
                                    ]);?>
                                    <?= Html::a('', ['/login/vkontakte'], [
                                        'class' => 'oauth-vk',
                                        'title' => 'Войти с помощью аккаунта Вконтакте',
                                        'rel' => 'external nofollow'
                                    ]);?>
                                    <?= Html::a('', ['/login/odnoklassniki'], [
                                        'class' => 'oauth-ok',
                                        'title' => 'Войти с помощью аккаунта Одноклассники',
                                        'rel' => 'external nofollow'
                                    ]);?>
                                    <?= Html::a('', ['/login/facebook'], ['
                                    class' => 'oauth-fb',
                                        'title' => 'Войти с помощью аккаунта Facebook',
                                        'rel' => 'external nofollow'
                                    ]);?>
                                </div>
                                <?php
                            } else {
                                $identity = Yii::$app->getUser()->getIdentity();
                                ?>
                                <div class="ava">
                                <?php
                                    echo "<span class='hidden-xs'>" . Html::beginTag('a', [
                                        'href' => '/me',
                                        'title' => 'Личный кабинет'
                                    ]);
                                    $options = [
                                        'alt' => 'Аватар',
                                        'title' => 'Аватар',
                                        'class' => 'avatar'
                                    ];
                                    if (isset($identity->photo) && $identity->photo) {
                                        echo Html::img(CFF::getThumb($identity->photo), $options);
                                    } else {
                                        if (isset($identity->gender) && $identity->gender == 2) {
                                            echo Html::img('/css/image/f_pic.png', $options);
                                        } else {
                                            echo Html::img('/css/image/m_pic.png', $options);
                                        }
                                    }
                                    echo "</span>" . Html::endTag('a');
                                    echo '<i class="glyphicon glyphicon-user hidden-lg hidden-md hidden-sm"></i> ';
                                    if (isset($identity->first_name)) {
                                        echo Html::a($identity->first_name, ['/me'], [
                                            'class' => 'enter link',
                                            'title' => 'Личный кабинет'
                                        ]);
                                    }
                                    echo ' <span class="hidden-xs"></br></span>' . Html::a('Выйти', ['/logout'], [
                                        'class' => 'enter link',
                                        'title' => 'Выйти из аккаунта'
                                    ]);
                                    ?>
                                </div>
                            <?php
                            } ?>
                        </div>
                        <div class="col-sm-4 logo">
                            <?php
                            if (isset($setting) && $setting->logo) {
                                echo Html::beginTag('a', [
                                    'href' => '/',
                                    'title' => 'На главную'
                                ]);
                                echo Html::img($setting->logo, [
                                    'alt' => 'На главную',
                                    'title' => 'На главную'
                                ]);
                                echo Html::endTag('a');
                            }?>
                        </div>
                        <div class="col-sm-4">
                        </div>
                    </div>
                </div>
<!--                Основное содержимое-->
                <div class="main">
                    <?= $content ?>
                </div>
<!--                Подвал сайта-->
                <div class="footer">
                    <div class="row">
                        <div class="col-sm-6 copyright">
                            <?php
                            if (isset($setting) && $setting->copyright) {
                                echo $setting->copyright;
                            } ?>
                        </div>
                        <div class="col-sm-6 counter">
                            <?php
                            if (isset($setting) && $setting->counter) {
                                echo $setting->counter;
                            } ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php $this->endBody() ?>
    </body>

</html>
<?php $this->endPage() ?>
