<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['password', 'email'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['email', 'email'],
            [['email'], 'filter', 'filter' => 'trim'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', 'Не правильно введен email или пароль.');
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('email', 'Ваш аккаунт заблокирован.');
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                $this->addError('email', 'Ваш аккаунт не подтвежден.');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'email' => 'Email',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    /**
     * Logs in a user using the provided email and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}
