lowBase - низкоуровневая Yii2 CMS для программистов
===================================================

lowBase - низкоуровневая CMS, включающая в себя готовую универсальную
систему администрирования сайта и Yii2 основу для самостоятельной разработки клиентского приложения. Разработка построена
на основе универсальных сущностей, которые задают структуру сайта (схожая структура в CMS ModX).

CMS lowbase составлена на основе независимых модулей:
[Модуль пользователей](https://github.com/lowbase/yii2-user "модуль пользователей")
[Модуль документов](https://github.com/lowbase/yii2-document "модуль документов")

Подробное описание возможностей модулей смотрите на странице с соответствующим модулем. Каждый из модулей может быть заменен на собственный.

[Демо панели администрирования](http://demo.lowbase.ru/admin "демо панели")
[Сайт-источник](http://lowbase.ru "сайт-источник")  


Установка lowBase
-----------------

* `composer create-project lowbase/yii2-cms project-name dev-master`
* Создаем пустую базу данных и настраиваем соединение с базой в файле config/db.php
```
'db' => [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=имя_базы_данных',
    'username' => 'пользватель',
    'password' => 'пароль',
    'charset' => 'utf8',
],
```
* Запускаем миграции коммандой `php yii migrate`
* Для работы EAUTH-авторизации необходимо зарегитсрировать приложение/сайт в соответствующих социальных сетях.
В файле config/web.php прописываем полученные ключи и id приложения:
```
  'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    // https://vk.com/editapp?act=create
                    'class' => 'lowbase\user\components\oauth\VKontakte',
                    'clientId' => '?',
                    'clientSecret' => '?',
                    'scope' => 'email'
                ],
                ...
            ]
        ]
```

##Основы проектирования сайта на lowBase

Разработка сайта ведется по принципу "Программирование от содержимого". Т.е. создаем структуру на основе документов в панели администрирования, а затем переходим к Frontend-модулю, делая самостоятельно вывод и пользовательский ввод, используя готовые модели. Либо производим эти действия параллельно: программист или верстальщик занимается пользовательским интерфейсом, а контент-менеджеры заполняют содержимое документов.

####Документ - универсальная сущность. Основа всего содержания

Документ - это:
* новость
* статья
* отзыв
* категория
* товар
* заказ магазина
* характеристка товара
* список со способами оплаты или доставки магазина
* сам способ оплаты или доставки
* тег для новости или статьи
* фотогалерея (основная страница вывода)
* альбом фотогалереи
* меню сайта
* пункт меню сайта
* ...

####Дополнительные поля

К каждому документу с помощью шаблона можно прикрепить дополнительные поля (различных типов). Допускаются мультизначения дополнительных полей с заданием минимального и максимального количества возможных значений.
Например:
* Цена товара (число)
* Сумма заказа (число)
* Рейтинг отзыва (список или число) 
* Вариант ответа опроса (список)
* Фотография альбома (файл с сервера)
* Теги (строка (мультиполе))
* ...

Как работать с CMS lowBase
--------------------------
В административной части формируется структура сайта на основе документов (меню, разделы, категории, статьи, различные списки) в древовидной иерархии.

Основные модели, с которыми придется взаимодействовать во клиентской части сайта: пользователи `app/models/User` и документы`app/models/Document` через контроллеры
`app/contollers/UserController` и `app/controllers/DocumentController`, которые являются пустыми заготовками приложения, унаследованные
от соответствующих модулей системы.

Можете не использовать эти заготовки (удалить), и создать модульную структуру, унаследовав ваши модули от соответствующих
модулей `\lowbase\document\Module` и `\lowbase\user\Module`. Возможности модулей и вызов виджетов смотрите в документации модулей.

####Работа с документами и с дополнительными полями

Значения дополнительных полей документа хранятся в массиве `$document->fields`

После получения самого документа массив не заполняется:

```
$document = app\models\Document::findOne($id);
print_r($document->fields);     //Array() - массив пуст
```

Для заполнения дополнительных полей документа используйте метод `fillFields()`

```
$document = app\models\Document::findOne($id);
$document->fillFields();
print_r($document->fields);     //Array([1] => ['name' => 'Теги', 'type' => 4, 'param' => '', 'min' => 0, 'max' => 2, 'data' => [[1] => ['value' => 'Тег_1', 'position' => ''], [2] => ...]], [2] => ...)

    /**
     * Значения дополнительных полей
     * Массив должен иметь следующую структуру:
     *
     * [$field_id] => [
     *                  'name' => 'Название дополнительного поля',
     *                  'type' => 'Тип дополнительного поля',
     *                  'param' => 'Параметр дополнительного поля',
     *                  'min' => 'Минимум значений',
     *                  'max' => 'Максимум значений',
     *                  'data' => [ $data_id => [
     *                                            'value' => 'Значение дополнительного поля'
     *                                            'position' => 'Позиция дополнительного поля'
     *                                             ],
     *                                           ...
     *                          ]
     *              ],
     * ...
     *
     * $field_id - ID дополнительного поля из БД, $data_id - ID записи значения дополнительного поля из БД
     * Если необходимо прикрепить новое значение 'data' к документу, то в качестве ключа используем 'new_'.$i, где
     * $i - идентификатор нового значения
     */
```
После сохранения документа
```
$document->save();
```
значения дополнительных полей будут сохранены в соответствующие таблицы.

Можно также получить значения дополнительных полей запросами к соответствующим таблицам (в зависимости от типа поля) БД напрямую

```
$data_values = \lowbase\document\models\ValueString::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all() // Получение значений дополнительного поля $field_id строкового типа
$data_values = \lowbase\document\models\ValueNumeric::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all() // Получение значений дополнительного поля $field_id числового типа
$data_values = \lowbase\document\models\ValueText::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all() // Получение значений дополнительного поля $field_id типа Текст
$data_values = \lowbase\document\models\ValueDate::find()->where(['field_id' => $field_id, 'document_id' => $document_id])->all() // Получение значений дополнительного поля $field_id типа Дата
```


