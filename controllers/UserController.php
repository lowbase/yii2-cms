<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\controllers;

/**
 * Пользователи
 *
 * Наследуется от контроллера документов модуля lowbase/yii2-user
 * Можете добавлять или изменять уже готовые action пользовательской части:
 * ------------------------------------------------
 * actionLogin - Страница авторизации
 * actionSignup - Страница регистрации
 * actionProfile - Страница профиля (личный кабинет)
 * actionShow($id) - Информация о пользователе
 * -------------------------------------------------
 * Если Вы хотите изменить лишь представления страниц без изменения логики,
 * можете воспользоваться возможностями модуля lowbase/yii2-user - пользовательское
 * отображение страниц (см. документацию модуля)
 *
 * Class UserController
 * @package app\controllers
 */
class UserController extends \lowbase\user\controllers\UserController
{
}
