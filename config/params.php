<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

return [
    'adminEmail' => 'lowbase@yandex.ru',
    //Action капчи в параметрах заполняется в случае если изменяются пути капчи
    //в конфигурации приложения и используются унаследованные модули или просто
    // модели и контроллеры от yii2-user.
    'captchaAction' => 'lowbase/user/captcha'
];
