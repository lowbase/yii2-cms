<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use common\models\AuthItemChild;
use common\models\Setting;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Главная'),
                    ],
                    [
                        'actions' => ['setting'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Настройки'),
                    ],
                    [
                        'actions' => ['manager'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Менеджер файлов'),
                    ],
                ],
            ],
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
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = 'simple';

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

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

    /**
     * @return string|\yii\web\Response
     */
    public function actionSetting()
    {
        $model = Setting::find()->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('setting-update-success');
            return $this->redirect(['setting']);
        }
        return $this->render('setting', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionManager()
    {
        return $this->render('manager');
    }
}
