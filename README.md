lowBase - низкоуровневая Yii2 CMS для программистов
===================================================

lowBase - низкоуровневая CMS (дословно: низкий уровень/основа), включающая в себя готовую универсальную
систему администрирования сайта и Yii2 основу для самостоятельной разработки клиентского приложения

Установка lowBase
-----------------

* Распаковываем файлы в директорию будущего сайта
* Создаем пустую базу данных и настраиваем соединение с базой в файле common/config/main-local.php
```
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=имя_базы_данных',
    'username' => 'пользватель',
    'password' => 'пароль',
    'charset' => 'utf8',
],
```
* В console/migrations/130524_201442_init.php устанавливаем необходимые данные администратора сайта
```
    const OPTIONS_FIELD = 10;
    const ADMIN_FIRST_NAME = 'Иван';
    const ADMIN_LAST_NAME = 'Иванов';
    const ADMIN_EMAIL = 'admin@lowbase.ru';
    const ADMIN_PASSWORD = 'admin';
```
* Запускаем миграции коммандой `php yii migrate`
* Для работы EAUTH-авторизации необходимо зарегитсрировать приложение/сайт в соответствующих социальных сетях.
В файле common/config/eauth.php прописываем полученные ключи и id приложения:
```
'facebook' => array(
                // register your app here: https://developers.facebook.com/apps/
                'class' => 'nodge\eauth\services\FacebookOAuth2Service',
                'clientId' => 'сюда_вбиваем_id',
                'clientSecret' => 'сюда_вбиваем_секретный_ключи',
            ),
```

