<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\imagine\Image;
use yii\base\NotSupportedException;
use Imagine\Image\ManipulatorInterface;
use common\helpers\CFF;
use \nodge\eauth\ErrorException;
use yii\web\HttpException;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email_confirm_token
 * @property string $email
 * @property string $phone
 * @property integer $gender
 * @property string $birthday
 * @property string $photo
 * @property string $role_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $oauth_vk_id
 * @property string $vk_page
 * @property string $oauth_fb_id
 * @property string $fb_page
 * @property string $oauth_ok_id
 * @property string $ok_page
 */
class User extends ActiveRecord implements IdentityInterface
{

    const FILES_PATH = 'attaches/user/';
    const PERSONAL_AREA = '/me';

    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    public $password; //пароль

    /**
     * @var \yii\web\UploadedFile
     */
    public $file;   //файл аватара
    public $profile; //профиль

    /**
     * @var \nodge\eauth\ServiceBase
     */

    /**
     *  Автозаполнение полей created_at и update_at
     */
    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => new Expression('NOW()'),
        ]];
    }

    /**
     * Статусы пользователя
     */
    public static function getStatusesArray()
    {
        return [
            self::STATUS_BLOCKED => 'Заблокирован',
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_WAIT => 'Не активен',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name'], 'required'],
            [['password', 'email'], 'required','on'=>'email_registration'],
            [['first_name', 'last_name', 'email', 'auth_key', 'password_hash',
                'password_reset_token', 'email_confirm_token', 'photo','password',
                'phone', 'oauth_vk_id', 'vk_page', 'oauth_fb_id', 'fb_page',
                'oauth_ok_id', 'ok_page'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 4],
            [['gender', 'status', 'role_id'], 'integer'],
            ['role_id', 'default', 'value' => 3],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => 'Данный Email уже используется в системе.'],
            [['birthday'], 'date', 'format' => 'd.M.yyyy'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name', 'email', 'phone'], 'filter', 'filter' => 'trim'],
            [['file'], 'image',
                'maxSize'       => 1024 * 1024,
                'minHeight' => 100,
                'skipOnEmpty' => true
            ],
            [['phone', 'email', 'last_name', 'auth_key', 'password_hash', 'password_reset_token',
            'email_confirm_token', 'gender', 'birthday', 'photo',
            'oauth_vk_id', 'vk_page', 'oauth_ok_id', 'ok_page', 'oauth_fb_id', 'fb_page'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'auth_key' => 'Ключ регистрации',
            'password_hash' => 'Хеш-пароля',
            'password_reset_token' => 'Токен восстановления пароля',
            'email_confirm_token' => 'Токен подтверждения Email',
            'email' => 'Email',
            'phone' => 'Телефон',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения',
            'photo' => 'Фото',
            'role_id' => 'Роль',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Обновлен',
            'oauth_vk_id' => 'ID Вконтакте',
            'vk_page' => 'Страница Вконтакте',
            'oauth_fb_id' => 'ID Facebook',
            'fb_page' => 'Страница Facebook',
            'oauth_ok_id' => 'ID Одноклассники',
            'ok_page' => 'Страница Одноклассники',
            'password' => 'Пароль',
            'file' => 'Фото',
            'profile' => 'Профиль',
        ];
    }

    /**
     * @param int|string $id
     * @return null|static
     * Идентификация пользователя по его id
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Поиск по токену не поддерживается.');
    }

    /**
     * @inheritdoc
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @inheritdoc
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @inheritdoc
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['email_confirm_token' => $email_confirm_token, 'status' => self::STATUS_WAIT]);
    }

    /**
     * @inheritdoc
     */
    public function generateEmailConfirmToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * @inheritdoc
     */
    public function getRole()
    {
        return $this->hasOne(AuthItem::className(), array('id' => 'role_id'));
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        /**
         * Если не привязаны аккаунты соц.сетей,
         * то нужно установить пароль для входа
         */
        if (!$this->isNewRecord && $this->scenario != 'search' && !$this->password_hash && !$this->password
            && !$this->oauth_vk_id && !$this->oauth_fb_id && !$this->oauth_ok_id) {
            $this->addError('password', 'Необходимо заполнить поле «Пароль».');
        }
        /*
         * Если не привязаны аккаунты соц.сетей,
         * то нужно установить email для входа
         */
        if (!$this->email && $this->scenario != 'search'
            && !$this->oauth_vk_id && !$this->oauth_fb_id && !$this->oauth_ok_id) {
            $this->addError('email', 'Необходимо заполнить поле «Email».');
        }
        /**
         * Ограничение возможности закрепления аккаунтов соц.сетей
         * если они уже закреплены за другими профилями
         */
        if (!Yii::$app->user->isGuest) {
            if ($this->oauth_vk_id) {
                $other_vk = User::find()
                    ->where(['oauth_vk_id' => $this->oauth_vk_id])
                    ->andWhere(['<>', 'id', $this->id])->one();
                if ($other_vk) {
                    $this->addError('oauth_vk_id', 'Аккаунт Вконтакте уже закреплен за другим пользователем');
                }
            }
            if ($this->oauth_fb_id) {
                $other_fb = User::find()
                    ->where(['oauth_fb_id' => $this->oauth_fb_id])
                    ->andWhere(['<>', 'id', $this->id])->one();
                if ($other_fb) {
                    $this->addError('oauth_fb_id', 'Аккаунт Facebook уже закреплен за другим пользователем');
                }
            }
            if ($this->oauth_ok_id) {
                $other_ok = User::find()
                    ->where(['oauth_ok_id' => $this->oauth_ok_id])
                    ->andWhere(['<>', 'id', $this->id])->one();
                if ($other_ok) {
                    $this->addError('oauth_ok_id', 'Аккаунт Одноклассники уже закреплен за другим пользователем');
                }
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->birthday) {
            $this->birthday = CFF::formatData($this->birthday);
        }
        if ($this->password) {
            $this->setPassword($this->password);
        }
        /**
         * Защита от подмены собственных данных
         * Производить изменение ролей может только администратор
         */
        $assign = AuthAssignment::find()->where(['user_id' => $this->id])->one();
        if ($assign) {
            if ($assign->item_name!=$this->role->name && !\Yii::$app->user->can('Администратор')) {
                $this->role = $assign->item_name; // Если Вы не администратор, оставляем роль какая была
            }
        }
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     * Добавление связи роль=>пользователь в таблицу auth_assignment (RBAC)
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        AuthAssignment::deleteAll(['user_id' => $this->id]);
        /** @var \common\models\AuthItem $role */
        $role = AuthItem::findOne($this->role_id);

        if ($role) {
            $assign = new AuthAssignment();
            $assign->user_id = (string) $this->id;
            $assign->item_name = $role->name;
            $assign->created_at = time();
            $assign->save();
        }
        return true;
    }

    /**
     * Инициализация пользователя
     * @return $this
     */
    public function initial()
    {
        if (!$this->isNewRecord) {
            $this->birthday = CFF::FormatData($this->birthday);
        }
        return $this;
    }

    /**
     * @param string (file - from \yii\web\UploadedFile, link - from adress)
     * @return bool
     * Сохренение аватара
     */
    public function savePhoto($source = 'file')
    {
        $ext = "." . end(explode(".", $this->file));
        if ($ext === ".") {
            $ext = '.jpg';
        }
        if (!file_exists(self::FILES_PATH)) {
            mkdir(self::FILES_PATH, 0777, true);
        }
        $name = time();
        if (!$this->isNewRecord) {
            $this->deletePhoto();
            $name .= '-' . $this->id;
        }
        $db_name = self::FILES_PATH . $name . $ext;
        if ($source == 'file') {
            $this->file->saveAs($db_name);
        } elseif ($source == 'link') {
            if (file_get_contents($this->photo)) {
                $content = file_get_contents($this->photo);
                file_put_contents($db_name, $content);
            }
        }
        $this->file = '/' . $db_name;
        Image::thumbnail($db_name, 65, 65, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
            ->save(self::FILES_PATH . $name . '_thumb' . $ext, ['quality' => 100]);
        $this->photo = $db_name;
        if (!$this->isNewRecord) {
            $db = User::getDb();
            $db->createCommand()->update('user', ['photo' => $db_name], ['id' => $this->id])->execute();
        }
        return true;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * Удаление аватара
     */
    public function deletePhoto()
    {
        if ($this->photo) {
            if (file_exists($this->photo)) {
                unlink($this->photo);
            }
            $thumb = CFF::getThumb($this->photo);
            if (file_exists($thumb)) {
                unlink($thumb);
            }
            if (!$this->isNewRecord) {
                $db = User::getDb();
                $db->createCommand()->update('user', ['photo' => null], ['id' => $this->id])->execute();
            }
        }
        return true;
    }

    /**
     * @return array
     * Массив всех пользователей
     */
    public static function getAll()
    {
        $users=[];
        $user = User::find()->all();
        if ($user) {
            foreach ($user as $u) {
                $users[$u->id] = $u->first_name . " " . $u->last_name . " (" . $u->id . ")";
            }
        }
        return $users;
    }

    /**
     * @return User|null
     * Регистрация нового пользователя
     */
    public function registration()
    {
        if ($this->validate()) {
            /**
             * Если все введенные данные верны, создаем пользователя, генерируем дополнительные поля
             */
            $user = new User();
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->status = User::STATUS_WAIT;
            $user->generateAuthKey();
            $user->generateEmailConfirmToken();
            /**
             * После сохранения отправляем на почту подтверждение регистрации
             */
            if ($user->save()) {
                Yii::$app->mailer->compose('confirmEmail', ['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail']])
                    ->setTo($this->email)
                    ->setSubject('Подтверждение регистрации на сайте')
                    ->send();
            }

            return $user;
        }

        return null;
    }

    /**
     * @param $serviceName
     * @param bool $auth
     * @throws \yii\base\InvalidConfigException
     */
    public static function loginByEAuth($serviceName, $auth = true)
    {
        /** @var \nodge\eauth\ServiceBase $eauth */
        $eauth = Yii::$app->get('eauth')->getIdentity($serviceName);
        try {
            if ($eauth->authenticate()) {
                $identity = User::findByEAuth($eauth);
                if ($auth) {
                    Yii::$app->getUser()->login($identity);
                }
                $eauth->redirect();
            } else {
                $eauth->cancel(Yii::$app->getUrlManager()->createAbsoluteUrl('site/login'));
            }
        } catch (ErrorException $e) {
            Yii::$app->getSession()->setFlash('error', 'EAuthException: '.$e->getMessage());
            $eauth->redirect($eauth->getCancelUrl());
        }
    }

    /**
     * @param $service \nodge\eauth\ServiceBase
     * @return bool|User|null
     * @throws ErrorException
     * @throws HttpException
     */
    protected static function findByEauth($service)
    {
        if (!$service->getIsAuthenticated()) {
            throw new ErrorException('Пользователь должен быть авторизирован.');
        }
        $serviceName = $service->getServiceName();
        $profile = $service->getAttributes();
        if (Yii::$app->user->isGuest) {
            switch ($serviceName) {
                case 'vkontakte':
                    $user = User::find()->where(['oauth_vk_id'=>$service->getId()])->one();
                    break;
                case 'facebook':
                    $user = User::find()->where(['oauth_fb_id'=>$service->getId()])->one();
                    break;
                case 'odnoklassniki':
                    $user = User::find()->where(['oauth_ok_id'=>$service->getId()])->one();
                    break;
            }
            if (isset($user) && $user) {
                return ($user->status == self::STATUS_ACTIVE) ? new self($user) : false;
            } else {
                return User::registrationByEauth($service); //Регистрация
            }
        } else {
            /**
             * Если пользователь уже авторизирован,
             * то закрепляем другие аккаунты за профилем
             */
            return User::assignEauth($serviceName, $profile);
        }
    }

    /**
     * @param $service \nodge\eauth\ServiceBase
     * @return bool|User
     * @throws HttpException
     */
    protected static function registrationByEauth($service)
    {
        $profile = $service->getAttributes();
        $serviceName = $service->getServiceName();

        $user = new User();
        $user->first_name = $profile['firstname'];
        $user->last_name = $profile['lastname'];
        switch ($serviceName) {
            case 'vkontakte':
                $user->oauth_vk_id = (string) $profile['id'];
                $user->vk_page = $profile['url'];
                $user->gender = $profile['gender'];
                if ($profile['photo_big']) {
                    $user->file = $profile['photo_big'];
                    $user->savePhoto('link');
                }
                break;
            case 'facebook':
                $user->oauth_fb_id = (string) $profile['id'];
                $user->fb_page = $profile['url'];
                $user->gender = ($profile['gender'] == 'male') ? 2 : ($profile['gender'] == 'female') ? 1 : 0;
                $fb_ava = 'http://graph.facebook.com/' . $profile['id'] . '/picture?type=large';
                $user->photo = $fb_ava;
                $user->savePhoto('link');
                break;
            case 'odnoklassniki':
                $user->oauth_ok_id = (string) $profile['id'];
                $user->ok_page = 'http://odnoklassniki/profile/' . $profile['id'];
                $user->gender = ($profile['gender'] == 'male') ? 2 : ($profile['gender'] == 'female') ? 1 : 0;
                if ($profile['photo']) {
                    $user->photo = $profile['photo'];
                    $user->savePhoto('link');
                }
                break;
        }
        if ($user->save()) {
            return $user;
        } else {
            throw new HttpException(400, 'Некорректный данные OAUTH');
        }
    }

    /**
     * @param $serviceName
     * @param $profile
     * @return User|null|static
     * Связывание профиля пользователя с
     * аккаунтами сторонних сайтов
     */
    public function assignEauth($serviceName, $profile)
    {
        $user = User::findOne(Yii::$app->user->id);
        if ($user) {
            switch ($serviceName) {
                case 'vkontakte':
                    $user->oauth_vk_id =  (string) $profile['id'];
                    $user->vk_page =  (string) $profile['url'];
                    break;
                case 'facebook':
                    $user->oauth_fb_id = (string) $profile['id'];
                    $user->fb_page = (string) $profile['url'];
                    break;
                case 'odnoklassniki':
                    $user->oauth_ok_id = (string) $profile['id'];
                    $user->ok_page =  (string) $profile['url'];
                    break;
            }
            if ($user->save()) {
                return true;
            }
        }
        return false;
    }
}
