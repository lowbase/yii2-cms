<?php

use yii\db\Migration;
use yii\db\Expression;


class m130524_201442_init extends Migration
{
    const OPTIONS_FIELD = 10;
    const OPTIONS_MESSAGE = 3;
    const ADMIN_FIRST_NAME = 'Иван';
    const ADMIN_LAST_NAME = 'Иванов';
    const ADMIN_EMAIL = 'admin@lowbase.ru';
    const ADMIN_PASSWORD = 'admin';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /*
         * Общие настройки сайта
         */
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'meta_description' => $this->text(),
            'meta_keywords' => $this->text(),
            'logo' => $this->string(),
            'favicon' => $this->string(),
            'copyright' => $this->text(),
            'counter' => $this->text(),
            'message_options_names' => $this->text(),
        ], $tableOptions);
        $this->insert('{{%setting}}', [
            'id' => 1,
            'name' => 'Название сайта',
            'title' => 'Заголовок сайта',
            'logo' => '/attaches/site/lowbase.png',
            'favicon' => '/attaches/site/lb.png'
        ]);

        /*
         * Пользователи
         */
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string(),
            'auth_key' => $this->string(),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string(),
            'email_confirm_token' => $this->string(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'gender' => $this->smallInteger(),
            'birthday' => $this->date(),
            'photo' => $this->string(),
            'role_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'oauth_vk_id' => $this->string(),
            'vk_page' => $this->string(),
            'oauth_fb_id' => $this->string(),
            'fb_page' => $this->string(),
            'oauth_ok_id' => $this->string(),
            'ok_page' => $this->string(),
        ], $tableOptions);
        $this->createIndex('idx-user-first_name', '{{%user}}', 'first_name');
        $this->createIndex('idx-user-last_name', '{{%user}}', 'last_name');
        $this->createIndex('idx-user-email', '{{%user}}', 'email');
        $this->createIndex('idx-user-status', '{{%user}}', 'status');
        $this->insert('{{%user}}', [
            'first_name' => self::ADMIN_FIRST_NAME,
            'last_name' => self::ADMIN_LAST_NAME,
            'email' => self::ADMIN_EMAIL,
            'gender' => 1,
            'role_id' => 1,
            'status' => 1,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
            'auth_key'=> Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash(self::ADMIN_PASSWORD),
        ]);

        /*
        * Правила (RBAC-миграция)
        */
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);

        /*
         * Роли (Модифифицированная RBAC-миграция)
         */
        $this->createTable('{{%auth_item}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'FOREIGN KEY (rule_name) REFERENCES ' . '{{%auth_rule}}' . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', 'type');
        $this->insert('{{%auth_item}}', [
            'id' => 1,
            'name' => 'Администратор',
            'description' => 'Разрешены все действия на сайте и в панели администрирования.',
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'id' => 2,
            'name' => 'Модератор',
            'description' => 'Пользователь с расширенными администратором правами',
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'id' => 3,
            'name' => 'Пользователь',
            'description' => 'Любой зарегистрировавшийся на сайте пользователь',
            'type' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Главная',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Настройки',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Менеджер файлов',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Допуски > Просмотр таблицы',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Допуски > Создание',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Допуски > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Допуски > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Роли > Просмотр таблицы',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Роли > Создание',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Роли > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Роли > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Пользователи > Просмотр таблицы',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Пользователи > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Пользователи > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Шаблоны > Просмотр таблицы',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Шаблоны > Создание',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Шаблоны > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Шаблоны > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Поиск по документам',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Создание',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Перемещение',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Документы > Предварительный просмотр',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Дополнительные поля > Поиск',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Сообщения > Просмотр таблицы',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Сообщения > Создание',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Сообщения > Редактирование',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%auth_item}}', [
            'name' => 'Администрирование: Сообщения > Удаление',
            'type' => 2,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        /*
         * Права доступа (Модифифицированная RBAC-миграция)
         */
        $this->createTable('{{%auth_item_child}}', [
            'id' => $this->primaryKey(),
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'FOREIGN KEY (parent) REFERENCES ' . '{{%auth_item}}' . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . '{{%auth_item}}' . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Главная',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Настройки',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Менеджер файлов',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Допуски > Просмотр таблицы',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Допуски > Создание',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Допуски > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Допуски > Удаление',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Роли > Просмотр таблицы',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Роли > Создание',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Роли > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Роли > Удаление',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Пользователи > Просмотр таблицы',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Пользователи > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Пользователи > Удаление',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Шаблоны > Просмотр таблицы',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Шаблоны > Создание',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Шаблоны > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Шаблоны > Удаление',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Поиск по документам',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Создание',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Перемещение',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Удаление',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Документы > Предварительный просмотр',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Дополнительные поля > Поиск',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Сообщения > Просмотр таблицы',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Сообщения > Создание',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Сообщения > Редактирование',
        ]);
        $this->insert('{{%auth_item_child}}', [
            'parent' => 'Администратор',
            'child' => 'Администрирование: Сообщения > Удаление',
        ]);

        /*
         * Связи ролей с пользователем (Модифицированная RBAC-миграция)
         */
        $this->createTable('{{%auth_assignment}}', [
            'id' => $this->primaryKey(),
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'FOREIGN KEY (item_name) REFERENCES ' . '{{%auth_item}}' . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->insert('{{%auth_assignment}}', [
            'item_name' => 'Администратор',
            'user_id' => 1,
            'created_at' => time(),
        ]);

        /*
         * Шаблоны
         */
        $fields = [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'path' => $this->string(),
        ];
        for ($i = 1; $i <= self::OPTIONS_FIELD; $i++) {
            $fields['option_' . $i . '_name'] = $this->string();
            $fields['option_' . $i . '_type'] = $this->smallInteger();
            $fields['option_' . $i . '_require'] = $this->smallInteger()->defaultValue(0);
            $fields['option_' . $i . '_param'] = $this->string();
        }
        $this->createTable('{{%template}}', $fields, $tableOptions);

        /*
         * Опции - задание дополнительных полей
         */
        $this->createTable('{{%option}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'param' => $this->string(),
            'require' => $this->smallInteger()->defaultValue(0),
            'multiple' => $this->smallInteger()->defaultValue(0),
            'template_id' => $this->integer()->notNull(),
            'FOREIGN KEY (template_id) REFERENCES ' . '{{%template}}' . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        /*
         * Документы
         */

        $fields = [
            'id' => $this->primaryKey(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'title' => $this->string(),
            'alias' => $this->string()->notNull(),
            'template_id' => $this->integer()->notNull(),
            'annotation' => $this->text(),
            'meta_description' => $this->text(),
            'meta_keywords' => $this->text(),
            'content' => $this->text(),
            'img' => $this->string(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'created_user_id' => $this->integer()->notNull(),
            'updated_user_id' => $this->integer(),
            'created_user_name' => $this->string()->notNull(),
            'updated_user_name' => $this->string(),
            'is_folder' => $this->smallInteger()->notNull()->defaultValue(0),
            'parent_id' => $this->integer()->notNull(),
            'parent_name' => $this->string(),
            'root_id' => $this->integer()->notNull(),
            'root_name' => $this->string()
        ];
        $fields_value = [
            'id' => 1,
            'lft' => 0,
            'rgt' => 2147483647,
            'depth' => 0,
            'name' => 'Сайт',
            'title' => 'Сайт',
            'alias' => 'site',
            'template_id' => 0,
            'annotation' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'content' => null,
            'img' => null,
            'status' => 1,
            'created_at' =>  new Expression('NOW()'),
            'updated_at' => null,
            'created_user_id' => 1,
            'updated_user_id' => null,
            'created_user_name' => self::ADMIN_FIRST_NAME . ' ' . self::ADMIN_LAST_NAME,
            'updated_user_name' => null,
            'is_folder' => 1,
            'parent_id' => 0,
            'parent_name' => null,
            'root_id' => 0,
            'root_name' => null,
        ];
        for ($i = 1; $i <= self::OPTIONS_FIELD; $i++) {
            $fields['option_' . $i ] = $this->text();
            $fields_value['option_' . $i ] = null;
        }
        $this->createTable('{{%document}}', $fields, $tableOptions);
        $this->insert('{{%document}}', $fields_value);

        /*
         * Дополнительные поля документов
         */
        $this->createTable('{{%field}}', [
            'id' => $this->primaryKey(),
            'option_id' => $this->integer()->notNull(),
            'document_id' => $this->integer()->notNull(),
            'position' => $this->integer(),
            'value' => $this->text()
        ], $tableOptions);

        /*
         * Сообщения
         */

        $fields = [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'content' => $this->text(),
            'attachment' => $this->string(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'created_user_id' => $this->integer()->notNull(),
            'updated_user_id' => $this->integer(),
            'created_user_name' => $this->string()->notNull(),
            'updated_user_name' => $this->string(),
            'for_document_id' => $this->integer(),
            'for_user_id' => $this->integer(),
            'parent_message_id' => $this->integer(),
            'created_ip' => $this->string(19)->notNull(),
            'FOREIGN KEY (for_document_id) REFERENCES ' . '{{%document}}' . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (for_user_id) REFERENCES ' . '{{%user}}' . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (parent_message_id) REFERENCES ' . '{{%message}}' . ' (id) ON DELETE CASCADE ON UPDATE CASCADE',
        ];

        for ($i = 1; $i <= self::OPTIONS_MESSAGE; $i++) {
            $fields['option_' . $i ] = $this->text();
        }
        $this->createTable('{{%message}}', $fields, $tableOptions);

    }

    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
        $this->dropTable('{{%message}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
        $this->dropTable('{{%option}}');
        $this->dropTable('{{%template}}');
        $this->dropTable('{{%document}}');
        $this->dropTable('{{%field}}');
    }
}
