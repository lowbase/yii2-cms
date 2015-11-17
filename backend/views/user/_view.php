<?php

use common\helpers\CFF;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
?>

<div class="usercart row">

    <div class="col-sm-2">
        <?php
        $options = [
            'alt' => 'Аватар',
            'title' => 'Аватар',
            'class' => 'avatar'
        ];
        if (isset($model->photo) && $model->photo) {
            echo Html::img(CFF::getThumb($model->photo), $options);
        } else {
            if (isset($model->gender) && $model->gender == 2) {
                echo Html::img('/css/image/f_pic.png', $options);
            } else {
                echo Html::img('/css/image/m_pic.png', $options);
            }
        }
        ?>
    </div>

    <div class="col-sm-2">
    <b>Пол:</b>
        <?= ($model->gender) ?
            (($model->gender == 1) ? "Мужской" : "Женский")
            : "<span class='hint'>Не указан</span>"; ?>
    <br><b>Дата рождения:</b>
        <?= ($model->birthday) ? CFF::formatData($model->birthday) : "<span class='hint'>Не указана</span>"; ?>
    </div>

    <div class="col-sm-8">
        <div class="social">
            <?php
            if ($model->vk_page) {
                echo Html::a('', $model->vk_page, [
                    'title' => 'Аккаунт Вконтакте',
                    'class' => 'vk-active',
                    'target' => '_blank'
                ]);
            } else {
                echo Html::a('', $model->vk_page, [
                    'title' => 'Аккаунт Вконтакте',
                    'class' => 'vk',
                ]);
            }
            if ($model->ok_page) {
                echo Html::a('', $model->ok_page, [
                    'title' => 'Аккаунт Одноклассники',
                    'class' => 'ok-active',
                    'target' => '_blank'
                ]);
            } else {
                echo Html::a('', $model->ok_page, [
                    'title' => 'Аккаунт Одноклассники',
                    'class' => 'ok',
                ]);
            }
            if ($model->fb_page) {
                echo Html::a('', $model->fb_page, [
                    'title' => 'Аккаунт Одноклассники',
                    'class' => 'fb-active',
                    'target' => '_blank'
                ]);
            } else {
                echo Html::a('', $model->fb_page, [
                    'title' => 'Аккаунт Одноклассники',
                    'class' => 'fb',
                ]);
            }
            ?>
         <div class="clear"></div>

        </div>
    </div>
</div>