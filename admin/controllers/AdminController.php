<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\controllers;

use lowbase\user\models\forms\LoginForm;
use lowbase\user\models\forms\PasswordResetForm;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Основной контроллер административной части сайта
 * Class SiteController
 * @package app\controllers
 */
class AdminController extends Controller
{
    public $layout = '@app/admin/layouts/main.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['manager'],
                'rules' => [
                    [
                        'actions' => ['manager'],
                        'allow' => true,
                        'roles' => ['fileManager'],
                    ],
                ],
            ],
        ];
    }

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

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $this->layout = '@app/admin/layouts/main-login.php';

            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                $this->redirect(['/admin']);
            }
            //Восстановление пароля
            $forget = new PasswordResetForm();
            if ($forget->load(Yii::$app->request->post()) && $forget->validate()) {
                if ($forget->sendEmail()) { // Отправлено подтверждение по Email
                    Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Ссылка с активацией нового пароля отправлена на Email.'));
                }
                $this->redirect(['/admin']);
            }
            return $this->render('@app/admin/views/login', ['model' => $model, 'forget' => $forget]);
        } else {
            if (Yii::$app->user->can('admin')) {
                return $this->render('@app/admin/views/index');
            }
        }
    }

    public function actionManager()
    {
        return $this->render('@app/admin/views/manager');
    }
}
