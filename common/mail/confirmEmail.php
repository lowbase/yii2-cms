<?php
use yii\helpers\Html;

if (isset($user) && $user) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['user/confirm', 'token' => $user->email_confirm_token]);
?>

Здравствуйте, <?= Html::encode($user->first_name) ?>!

Для подтверждения адреса пройдите по ссылке:

<?= Html::a(Html::encode($confirmLink), $confirmLink) ?>

Если Вы не регистрировались у на нашем сайте, то просто удалите это письмо.

<?php
}
?>