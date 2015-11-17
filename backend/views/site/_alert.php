<?php

use kartik\widgets\Alert;

$alerts =[
    'setting-update-success' => 'Настройки сайта сохранены.',
    'user-update-success' => 'Пользователь сохранен.',
    'user-delete-success' => 'Пользователь удален.',
    'users-delete-success' => 'Пользователи удалены.',
    'user-open-success' => 'Пользователь активирован.',
    'users-open-success' => 'Пользователи активированы.',
    'user-close-success' => 'Пользователь заблокирован.',
    'users-close-success' => 'Пользователи заблокированы.',
    'template-delete-success' => 'Шаблон удален.',
    'templates-delete-success' => 'Шаблоны удалены.',
    'template-create-success' => 'Новый шаблон создан.',
    'template-update-success' => 'Шаблон сохранен.',
    'option-create-success' => 'Новое дополнительное поле создано.',
    'option-update-success' => 'Дополнительное поле сохранено.',
    'option-delete-success' => 'Дополнительное поле удалено.',
    'options-delete-success' => 'Дополнительные поля удалены.',
    'permission-delete-success' => 'Допуск удален.',
    'permissions-delete-success' => 'Допуски удалены.',
    'permission-create-success' => 'Новый допуск создан.',
    'permission-update-success' => 'Допуск сохранен.',
    'role-create-success' => 'Новая роль создана',
    'rights-create-success' => 'Новая точка доступа создана',
    'role-update-success' => 'Роль сохранена',
    'rights-update-success' => 'Точка доступа сохранена',
    'role-delete-success' => 'Роль удалена',
    'rights-delete-success' => 'Точка доступа удалена',
    'roles-rights-delete-success' => 'Позиции удалены',
    'role-rights-delete-success' => 'Позиция удалена',
    'document-create-success' => 'Новый документ создан',
    'document-update-success' => 'Документ сохранен',
    'document-update-move-success' => 'Документ сохранен и перемещен',
    'documents-publicate-success' => 'Документ(ы) опубликованы',
    'documents-close-success' => 'Документ(ы) сняты с публикации',
    'document-delete-success' => 'Документ удален',
    'documents-delete-success' => 'Документ(ы) удалены',
    'field-delete-success' => 'Значение удалено',
    'image-delete-success' => 'Изображение удалено',
    'message-create-success' => 'Новое сообщение отправлено',
    'message-update-success' => 'Сообщение отредактировано',
    'message-publicate-success' => 'Сообщения опубликованы',
    'message-close-success' => 'Сообщения скрыты',
    'message-delete-success' => 'Сообщение удалено',
    'messages-delete-success' => 'Сообщения удалены'
];

$flash = (count(Yii::$app->session->getAllFlashes())) ? key(Yii::$app->session->getAllFlashes()) : ' ';

if (in_array($flash, array_keys($alerts))) {
    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'body' => $alerts[$flash],
        'delay' => 2000
    ]);
}
