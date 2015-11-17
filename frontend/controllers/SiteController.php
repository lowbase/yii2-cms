<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use common\models\Setting;
use common\models\User;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'eauth' => [
                // required to disable csrf validation on OpenID requests
                'class' => \nodge\eauth\openid\ControllerBehavior::className(),
                'only' => ['login'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $setting = $this->getSetting();

        \Yii::$app->view->title = $setting->title;
        if ($setting->meta_description) {
            \Yii::$app->view->registerMetaTag([
                'name' => 'meta_description',
                'content' => $setting->meta_description
            ]);
        }
        if ($setting->meta_keywords) {
            \Yii::$app->view->registerMetaTag([
                'name' => 'meta_keywords',
                'content' => $setting->meta_keywords
            ]);
        }

        return $this->render('index', [
            'setting' => $setting
        ]);
    }

    /*
     * @return модель с настройками сайта
     * По умолчанию в настройка всего одна запись с ID = 1
     * (созданная из миграций).
     * По желанию можно хранить несколько настроек сайта в базе
     */
    protected function getSetting($id = 1)
    {
        $model = Setting::find()->where(['id' => $id])->one();
        if (($model) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Настройки сайта не найдены.');
        }
    }


    public function actionLogin()
    {
        /**
         * EAUTH-авторизация с помощью аккаунтов соц. сетей
         */
        $serviceName = Yii::$app->getRequest()->getQueryParam('service');
        if (isset($serviceName)) {
            User::loginByEAuth($serviceName);
        }
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        /**
         * Классическая аторизация через форму с помощью Email
         */
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
