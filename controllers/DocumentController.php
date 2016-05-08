<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\controllers;

/**
 * Документы
 *
 * Наследуется от контроллера документов модуля lowbase/yii2-document
 * Можете добавлять или изменять уже готовые action пользовательской части:
 * ------------------------------------------------
 * actionShow - Страница отображения документа на сайте по шаблону
 * -------------------------------------------------
 * При изменении actionShow (отображение документа) рекомендуется производить
 * рендер переменной $template
 * $template = (isset($model->template) && $model->template->path) ? $model->template->path : '@vendor/lowbase/yii2-document/views/document/template/default';
 * чтобы не потерять функциональность шаблонов документов при отображении.
 *
 * Если Вы хотите изменить лишь представления страниц без изменения логики,
 * можете воспользоваться возможностями модуля lowbase/yii2-document - пользовательское
 * отображение страниц (см. документацию модуля)
 *
 * Class DocumentController
 * @package app\controllers
 */
class DocumentController extends \lowbase\document\controllers\DocumentController
{
}
