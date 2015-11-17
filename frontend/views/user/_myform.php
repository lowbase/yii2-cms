<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$this->registerJs('
    $(document).ready(function(){
        $(".change_password").click(function(){
            $(".new_password").toggle();
            var display = $(".new_password").css("display");
            if (display=="none")
            {
                $(".field-user-password input").val("");
                $(".change_password").text("Изменить пароль");
            }
            else
                $(".change_password").text("Отмена");
        });
    });
');

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class'=>'form',
        'enctype'=>'multipart/form-data'
    ]]);
    ?>

    <div class="row">

        <div class="col-sm-4">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'gender')->dropDownList([
                '' => '',
                '1' => 'Мужской',
                '2' => 'Женский']); ?>

            <div class="form-group">
                <label>День рождения</label>
                <?php $birthday  =  ($model->birthday) ? $model->birthday : null;?>
                <?php echo DatePicker::widget([
                    'name' => 'User[birthday]',
                    'value' => $birthday,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true
                    ]
                ]); ?>

            </div>

            <div class="form-group new_password">
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
            </div>

            <p><a href="javascript:void(0);" class="change_password lnk">Изменить пароль</a></p>

            <div class="form-group">
                <?php
                echo Html::submitButton('Обновить данные', ['class' => 'btn btn-primary']) . " ";
                echo Html::a('На главную', ['/'], ['class' => 'btn btn-default']) . " ";
                if (\Yii::$app->user->can('Администратор')) {
                    echo Html::a('В админку', '/admin', ['class' => 'btn btn-default']);
                }
                ?>
            </div>

        </div>

        <div class="col-sm-4">

            <div class="big_avatar">
                <?php
                if ($model->photo) {
                    echo Html::beginTag('a', [
                            'href' => $model->photo,
                            'title' => 'Фото'
                        ]);
                        echo Html::img($model->photo, ['class' => 'big_avatar img-thumbnail']);
                        echo Html::endTag('a');
                        echo "<p>" . Html::a('Удалить фото', ['/user/deletephoto'], [
                            'class' => 'lnk delete_photo',
                            'title' => 'Удалить фото',
                            'rel' => 'external nofollow'
                        ]) . "</p>";
                } elseif ($model->gender == 2) {
                        echo Html::img("/css/image/f_ava.png", ['class' => 'big_avatar img-thumbnail']);
                } else {
                        echo Html::img("/css/image/m_ava.png", ['class' => 'big_avatar img-thumbnail']);
                }
                ?>
            </div>

            <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>

            <?php
            if ($model->oauth_vk_id || $model->oauth_ok_id || $model->oauth_fb_id) { ?>
                <p class="hint-block">
                    Чтобы отвязать все социальные сети необходимо,
                    чтобы у текущего пользователя были установлены Email
                    и Пароль для будущего входа.
                </p>
            <?php
            } else { ?>
                <p class="hint-block">
                    Привяжите к своей странице
                    аккаунты социальных сетей для быстрого входа на сайт.
                </p>
            <?php
            }?>

            <div class="social_log social">
                <?php
                if ($model->oauth_vk_id) {
                    echo Html::a('', ['/disable/vkontakte'], [
                        'class' => 'vk-active',
                        'title' => 'Открепить аккаунт Вконтакте',
                        'rel' => 'external nofollow'
                    ]);
                } else {
                    echo Html::a('', ['/enable/vkontakte'], [
                        'class' => 'vk',
                        'title' => 'Закрепить аккаунт Вконтакте',
                        'rel' => 'external nofollow'
                    ]);
                }
                if ($model->oauth_ok_id) {
                    echo Html::a('', ['/disable/odnoklassniki'], [
                        'class' => 'ok-active',
                        'title' => 'Открепить аккаунт Одноклассники',
                        'rel' => 'external nofollow'
                    ]);
                } else {
                    echo Html::a('', ['/enable/odnoklassniki'], [
                        'class' => 'ok',
                        'title' => 'Закрепить аккаунт Одноклассники',
                        'rel' => 'external nofollow'
                    ]);
                }
                if ($model->oauth_fb_id) {
                    echo Html::a('', ['/disable/facebook'], [
                        'class' => 'fb-active',
                        'title' => 'Открепить аккаунт Facebook',
                        'rel' => 'external nofollow'
                    ]);
                } else {
                    echo Html::a('', ['/enable/facebook'], [
                        'class' => 'fb',
                        'title' => 'Закрепить аккаунт Facebook',
                        'rel' => 'external nofollow'
                    ]);
                }
                ?>
                <div class="clear"></div>
            </div>
        </div>

        <div class="col-sm-4">
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
