<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

use lowbase\user\models\AuthItem;
use lowbase\user\models\User;
use yii\db\Schema;
use yii\db\Migration;

class m160505_213256_lowbase extends Migration
{
    //Администратор по умолчанию
    const ADMIN_FIRST_NAME = 'Имя_администратора';
    const ADMIN_LAST_NAME = 'Фамилия_администратора';
    const ADMIN_EMAIL = 'admin@example.ru';
    const ADMIN_PASSWORD = 'admin';

    //Модератор по умолчанию
    const MODERATOR_FIRST_NAME = 'Имя_модератора';
    const MODERATOR_LAST_NAME = 'Фамилия_модератора';
    const MODERATOR_EMAIL = 'moderator@example.ru';
    const MODERATOR_PASSWORD = 'moderator';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        //Таблица страны country
        $this->createTable('{{%lb_country}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING.'(255) NOT NULL',
            'currency_code' => Schema::TYPE_STRING.'(5) NOT NULL',
            'currency' => Schema::TYPE_STRING.' NULL DEFAULT NULL'
        ], $tableOptions);

        //Таблица города city
        $this->createTable('{{%lb_city}}', [
            'id' => Schema::TYPE_PK,
            'country_id' => Schema::TYPE_INTEGER.'(11) NOT NULL',
            'city' => Schema::TYPE_STRING.'(255) NOT NULL',
            'state' => Schema::TYPE_STRING.'(255) NULL DEFAULT NULL',
            'region' => Schema::TYPE_STRING.'(255) NOT NULL',
            'biggest_city' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
        ], $tableOptions);

        //Ключи и индексы
        $this->addForeignKey('city_country_id_fk', '{{%lb_city}}', 'country_id', '{{%lb_country}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('city_city_index', '{{%lb_city}}', 'city');

        //Таблица пользователей user
        $this->createTable('{{%lb_user}}', [
            'id' => Schema::TYPE_PK,
            'first_name' => Schema::TYPE_STRING . '(100) NOT NULL',
            'last_name' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NULL DEFAULT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'password_reset_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'email_confirm_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'email' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'image' => Schema::TYPE_STRING.' NULL DEFAULT NULL',
            'sex' => Schema::TYPE_SMALLINT.' NULL DEFAULT NULL',
            'birthday' => Schema::TYPE_DATE . ' NULL DEFAULT NULL',
            'phone' => Schema::TYPE_STRING . '(100) NULL DEFAULT NULL',
            'country_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'city_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'status' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT ' . User::STATUS_WAIT,
            'address' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NULL DEFAULT NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'login_at' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы пользователей user
        $this->addForeignKey('user_country_id_fk', '{{%lb_user}}', 'country_id', '{{%lb_country}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('user_city_id_fk', '{{%lb_user}}', 'city_id', '{{%lb_city}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('user_name_index', '{{%lb_user}}', ['first_name', 'last_name']);
        $this->createIndex('user_email_index', '{{%lb_user}}', 'email');
        $this->createIndex('user_status_index', '{{%lb_user}}', 'status');

        //Предустановленные значения таблицы пользователей user
        $this->batchInsert('lb_user', [
            'id',
            'first_name',
            'last_name',
            'email',
            'auth_key',
            'password_hash',
            'status',
            'created_at',
            'updated_at'
        ], [
            [
                1,
                self::ADMIN_FIRST_NAME,
                self::ADMIN_LAST_NAME,
                self::ADMIN_EMAIL,
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash(self::ADMIN_PASSWORD),
                User::STATUS_ACTIVE,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ],
            [
                2,
                self::MODERATOR_FIRST_NAME,
                self::MODERATOR_LAST_NAME,
                self::MODERATOR_EMAIL,
                Yii::$app->security->generateRandomString(),
                Yii::$app->security->generatePasswordHash(self::MODERATOR_PASSWORD),
                User::STATUS_ACTIVE,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ]
        ]);

        //Таблица авторизации пользователя user_oauth_key
        $this->createTable('{{%lb_user_oauth_key}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'provider_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'provider_user_id' => Schema::TYPE_STRING.'(255) NOT NULL',
            'page' => Schema::TYPE_STRING.'(255) NULL DEFAULT NULL'
        ], $tableOptions);

        //Индексы и ключи таблицы авторизации пользователя user_oauth_key
        $this->addForeignKey('user_oauth_key_user_id_fk', '{{%lb_user_oauth_key}}', 'user_id', '{{%lb_user}}', 'id', 'CASCADE', 'CASCADE');

        /**
         * Миграции RBAC
         */

        //Таблица правил auth_rule
        $this->createTable('{{%lb_auth_rule}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER

        ], $tableOptions);

        //Индексы и ключи таблицы правил auth_rule
        $this->addPrimaryKey('auth_rule_pk', '{{%lb_auth_rule}}', 'name');

        //Предустановленные значения таблицы правил auth_rule
        $this->insert('lb_auth_rule', [
            'name' => 'AuthorRule',
            'data' => 'O:29:"lowbase\user\rules\AuthorRule":3:{s:4:"name";s:10:"AuthorRule";s:9:"createdAt";N;s:9:"updatedAt";N;}',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        //Таблица ролей и допусков auth_item
        $this->createTable('{{%lb_auth_item}}', [
            'name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER.' NOT NULL',
            'description' => Schema::TYPE_TEXT.' NOT NULL',
            'rule_name' => Schema::TYPE_STRING.'(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);

        //Индексы и ключи таблицы ролей и допусков auth_item
        $this->addPrimaryKey('auth_item_name_pk', '{{%lb_auth_item}}', 'name');
        $this->addForeignKey('auth_item_rule_name_fk', '{{%lb_auth_item}}', 'rule_name', '{{%lb_auth_rule}}',  'name', 'SET NULL', 'CASCADE');
        $this->createIndex('auth_item_type_index', '{{%lb_auth_item}}', 'type');

        //Предустановленные значения таблицы ролей и допусков auth_item
        $this->batchInsert('lb_auth_item', ['name', 'type', 'description', 'rule_name', 'created_at', 'updated_at'], [
            ['administrator', AuthItem::TYPE_ROLE, 'Администратор', NULL, time(), time()],
            ['moderator', AuthItem::TYPE_ROLE, 'Модератор', NULL, time(), time()],
            ['userUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование пользователя', NULL, time(), time()],
            ['userDelete', AuthItem::TYPE_PERMISSION, 'Удаление пользователя', NULL, time(), time()],
            ['userManager', AuthItem::TYPE_PERMISSION, 'Менеджер пользователей', NULL, time(), time()],
            ['userView', AuthItem::TYPE_PERMISSION, 'Просмотр карточки пользователя', NULL, time(), time()],
            ['roleCreate', AuthItem::TYPE_PERMISSION, 'Создание роли / допуска', NULL, time(), time()],
            ['roleUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование роли / допуска', NULL, time(), time()],
            ['roleDelete', AuthItem::TYPE_PERMISSION, 'Удаление роли / допуска', NULL, time(), time()],
            ['roleManager', AuthItem::TYPE_PERMISSION, 'Менеджер ролей / допусков', NULL, time(), time()],
            ['roleView', AuthItem::TYPE_PERMISSION, 'Просмотр роли / допуска', NULL, time(), time()],
            ['ruleCreate', AuthItem::TYPE_PERMISSION, 'Создание правил контроля доступа', NULL, time(), time()],
            ['ruleDelete', AuthItem::TYPE_PERMISSION, 'Удаление правил контроля доступа', NULL, time(), time()],
            ['ruleManager', AuthItem::TYPE_PERMISSION, 'Менеджер правил контроля доступа', NULL, time(), time()],
            ['ruleView', AuthItem::TYPE_PERMISSION, 'Просмотр правил контроля доступа', NULL, time(), time()],
            ['countryCreate', AuthItem::TYPE_PERMISSION, 'Создание страны', NULL, time(), time()],
            ['countryUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование страны', NULL, time(), time()],
            ['countryDelete', AuthItem::TYPE_PERMISSION, 'Удаление страны', NULL, time(), time()],
            ['countryManager', AuthItem::TYPE_PERMISSION, 'Менеджер стран', NULL, time(), time()],
            ['countryView', AuthItem::TYPE_PERMISSION, 'Просмотр страны', NULL, time(), time()],
            ['cityCreate', AuthItem::TYPE_PERMISSION, 'Создание населенного пункта', NULL, time(), time()],
            ['cityUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование населенного пункта', NULL, time(), time()],
            ['cityDelete', AuthItem::TYPE_PERMISSION, 'Удаление населенного пункта', NULL, time(), time()],
            ['cityManager', AuthItem::TYPE_PERMISSION, 'Менеджер населенных пунктов', NULL, time(), time()],
            ['cityView', AuthItem::TYPE_PERMISSION, 'Просмотр населенного пункта', NULL, time(), time()],
            ['documentCreate', AuthItem::TYPE_PERMISSION, 'Создание документа', NULL, time(), time()],
            ['documentUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование документа', NULL, time(), time()],
            ['documentDelete', AuthItem::TYPE_PERMISSION, 'Удаление документа', NULL, time(), time()],
            ['documentManager', AuthItem::TYPE_PERMISSION, 'Менеджер документов', NULL, time(), time()],
            ['documentView', AuthItem::TYPE_PERMISSION, 'Просмотр документа', NULL, time(), time()],
            ['templateCreate', AuthItem::TYPE_PERMISSION, 'Создание шаблона', NULL, time(), time()],
            ['templateUpdate', AuthItem::TYPE_PERMISSION, 'Редактирование шаблона', NULL, time(), time()],
            ['templateDelete', AuthItem::TYPE_PERMISSION, 'Удаление шаблона', NULL, time(), time()],
            ['templateManager', AuthItem::TYPE_PERMISSION, 'Менеджер шаблона', NULL, time(), time()],
            ['templateView', AuthItem::TYPE_PERMISSION, 'Просмотр шаблона', NULL, time(), time()],
            ['fileManager', AuthItem::TYPE_PERMISSION, 'Файловый менеджер', NULL, time(), time()],
            ['admin', AuthItem::TYPE_PERMISSION, 'Рабочий стол панели администрирования', NULL, time(), time()],
        ]);

        //Таблица разрешений auth_item_child
        $this->createTable('{{%lb_auth_item_child}}', [
            'parent' => Schema::TYPE_STRING.'(64) NOT NULL',
            'child' => Schema::TYPE_STRING.'(64) NOT NULL'
        ], $tableOptions);

        //Индексы и ключи таблицы разрешений auth_item_child
        $this->addPrimaryKey('auth_item_child_pk', '{{%lb_auth_item_child}}', array('parent', 'child'));
        $this->addForeignKey('auth_item_child_parent_fk', '{{%lb_auth_item_child}}', 'parent', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_item_child_child_fk', '{{%lb_auth_item_child}}', 'child', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        //Предустановленные значения таблицы разрешений auth_item_child
        $this->batchInsert('lb_auth_item_child', ['parent', 'child'], [
            ['moderator', 'userManager'],
            ['moderator', 'userView'],
            ['moderator', 'roleManager'],
            ['moderator', 'roleView'],
            ['moderator', 'countryManager'],
            ['moderator', 'countryView'],
            ['moderator', 'cityManager'],
            ['moderator', 'cityView'],
            ['administrator', 'moderator'],
            ['administrator', 'userUpdate'],
            ['administrator', 'userDelete'],
            ['administrator', 'roleCreate'],
            ['administrator', 'roleUpdate'],
            ['administrator', 'roleDelete'],
            ['administrator', 'ruleCreate'],
            ['administrator', 'ruleDelete'],
            ['administrator', 'ruleView'],
            ['administrator', 'ruleManager'],
            ['administrator', 'countryCreate'],
            ['administrator', 'countryUpdate'],
            ['administrator', 'countryDelete'],
            ['administrator', 'cityCreate'],
            ['administrator', 'cityUpdate'],
            ['administrator', 'cityDelete'],
            ['moderator', 'documentCreate'],
            ['moderator', 'documentUpdate'],
            ['moderator', 'documentDelete'],
            ['moderator', 'documentManager'],
            ['moderator', 'documentView'],
            ['administrator', 'templateCreate'],
            ['administrator', 'templateUpdate'],
            ['administrator', 'templateDelete'],
            ['moderator', 'templateManager'],
            ['moderator', 'templateView'],
            ['moderator', 'fileManager'],
            ['moderator', 'admin'],
        ]);

        //Таблица связи ролей auth_assignment
        $this->createTable('{{%lb_auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING.'(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER.'(11) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER
        ], $tableOptions);

        //Индексы и ключи таблицы связи ролей auth_assignment
        $this->addPrimaryKey('auth_assignment_pk', '{{%lb_auth_assignment}}', array('item_name', 'user_id'));
        $this->addForeignKey('auth_assignment_item_name_fk', '{{%lb_auth_assignment}}', 'item_name', '{{%lb_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('auth_assignment_user_id_fk', '{{%lb_auth_assignment}}', 'user_id', '{{%lb_user}}', 'id', 'CASCADE', 'CASCADE');

        //Предустановленные значения таблицы связи ролей auth_assignment
        $this->batchInsert('lb_auth_assignment', ['item_name', 'user_id', 'created_at', 'updated_at'], [
            ['administrator', 1, time(), time()],
            ['moderator', 2, time(), time()],
        ]);

        //Таблица шаблонов template
        $this->createTable('{{%lb_template}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'path' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ] , $tableOptions);

        //Индексы и ключи таблицы шаблонов template
        $this->createIndex('template_name_index', '{{%lb_template}}', 'name');

        //Таблица документов document
        $this->createTable('{{%lb_document}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'alias' => Schema::TYPE_STRING . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'meta_keywords' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'meta_description' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'annotation' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'content' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'image' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'status' =>Schema::TYPE_SMALLINT. ' NOT NULL DEFAULT 1',
            'is_folder' =>Schema::TYPE_SMALLINT. ' NOT NULL DEFAULT 0',
            'parent_id' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'template_id' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'created_at' =>Schema::TYPE_DATETIME. ' NOT NULL',
            'updated_at' =>Schema::TYPE_DATETIME. ' NULL DEFAULT NULL',
            'created_by' =>Schema::TYPE_INTEGER. ' NOT NULL',
            'updated_by' =>Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
            'position' => Schema::TYPE_INTEGER. ' NULL DEFAULT NULL',
        ] , $tableOptions);

        //Индексы и ключи таблицы документов document
        $this->addForeignKey('document_parent_id_fk', '{{%lb_document}}', 'parent_id', '{{%lb_document}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('document_template_id_fk', '{{%lb_document}}', 'template_id', '{{%lb_template}}', 'id', 'SET NULL', 'CASCADE');
        $this->createIndex('document_name_index', '{{%lb_document}}', 'name');
        $this->createIndex('document_alias_index', '{{%lb_document}}', 'alias');
        $this->createIndex('document_status_index', '{{%lb_document}}', 'status');

        //Дополнительные поля
        $this->createTable('{{%lb_field}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'template_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'param' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'min' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'max' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
        ], $tableOptions);

        //Индексы и ключи таблицы полей field
        $this->addForeignKey('field_template_id_fk', '{{%lb_field}}', 'template_id', '{{%lb_template}}', 'id', 'CASCADE', 'CASCADE');

        //Числовые значения дополнительных полей
        $this->createTable('{{%lb_value_numeric}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_DOUBLE . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы числовых значений дополнительных полей
        $this->addForeignKey('value_numeric_document_id_fk', '{{%lb_value_numeric}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_numeric_field_id_fk', '{{%lb_value_numeric}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_numeric_name_index', '{{%lb_value_numeric}}', 'value');

        //Строковые значения дополнительных полей
        $this->createTable('{{%lb_value_string}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы строковых значений дополнительных полей
        $this->addForeignKey('value_string_document_id_fk', '{{%lb_value_string}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_string_field_id_fk', '{{%lb_value_string}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_string_name_index', '{{%lb_value_string}}', 'value');

        //Текстовые значения дополнительных полей
        $this->createTable('{{%lb_value_text}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы текстовых значений дополнительных полей
        $this->addForeignKey('value_text_document_id_fk', '{{%lb_value_text}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_text_field_id_fk', '{{%lb_value_text}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');

        //Значения дат дополнительных полей
        $this->createTable('{{%lb_value_date}}', [
            'id' => Schema::TYPE_PK,
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'field_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'position' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
            'value' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы значений дат дополнительных полей
        $this->addForeignKey('value_date_document_id_fk', '{{%lb_value_date}}', 'document_id', '{{%lb_document}}', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('value_date_field_id_fk', '{{%lb_value_date}}', 'field_id', '{{%lb_field}}', 'id', 'NO ACTION', 'CASCADE');
        $this->createIndex('value_date_name_index', '{{%lb_value_date}}', 'value');

        //Таблица просмотров документов visit
        $this->createTable('{{%lb_visit}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NOT NULL',
            'user_agent' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы таблицы просмотров документов visit
        $this->addForeignKey('visit_document_id_fk', '{{%lb_visit}}', 'document_id', '{{%lb_document}}', 'id', 'CASCADE', 'CASCADE');

        //Таблица просмотров документов visit
        $this->createTable('{{%lb_like}}', [
            'id' => Schema::TYPE_PK,
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'ip' => Schema::TYPE_STRING . '(20) NOT NULL',
            'user_agent' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NULL DEFAULT NULL',
        ], $tableOptions);

        //Индексы и ключи таблицы таблицы просмотров документов visit
        $this->addForeignKey('like_document_id_fk', '{{%lb_like}}', 'document_id', '{{%lb_document}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%lb_auth_assignment}}');
        $this->dropTable('{{%lb_auth_item_child}}');
        $this->dropTable('{{%lb_auth_item}}');
        $this->dropTable('{{%lb_auth_rule}}');
        $this->dropTable('{{%lb_user_oauth_key}}');
        $this->dropTable('{{%lb_user}}');
        $this->dropTable('{{%lb_city}}');
        $this->dropTable('{{%lb_country}}');
        $this->dropTable('{{%lb_like}}');
        $this->dropTable('{{%lb_visit}}');
        $this->dropTable('{{%lb_value_numeric}}');
        $this->dropTable('{{%lb_value_string}}');
        $this->dropTable('{{%lb_value_text}}');
        $this->dropTable('{{%lb_value_date}}');
        $this->dropTable('{{%lb_field}}');
        $this->dropTable('{{%lb_document}}');
        $this->dropTable('{{%lb_template}}');
    }
}
