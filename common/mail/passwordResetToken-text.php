<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/newpass', 'token' => $user->password_reset_token]);
?>
Здравствуйте, <?= $user->first_name ?>,

Нажмите на ссылку для восстановления пароля:

<?= $resetLink ?>
