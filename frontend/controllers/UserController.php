<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use common\models\User;
use common\models\EmailConfirmForm;
use common\models\PasswordResetRequestForm;
use common\models\ResetPasswordForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Регистрация пользователя
     */
    public function actionCreate()
    {
        $model = new User();

        $model->scenario = "email_registration";

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->registration()) {
                Yii::$app->getSession()->setFlash('send-email', 'На Email отправлено письмо. Подтвердите ваш электронный адрес.');
                return $this->redirect(['/registration']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionConfirm($token)
    {
        try {
            $model = new EmailConfirmForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->confirmEmail()) {
            Yii::$app->getSession()->setFlash('confirm-success', 'Спасибо! Ваш Email успешно подтверждён.');
        } else {
            Yii::$app->getSession()->setFlash('confirm-error', 'Ошибка подтверждения Email.');
        }

        return $this->redirect(['/login']);
    }

    /**
     * Личный кабинет пользователя
     */
    public function actionMe()
    {
        $model = $this->findModel(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->validate()) {
                if ($model->file) {
                    $model->savePhoto();
                }
                if ($model->save()) {
                    Yii::$app->getSession()->setFlash('me-update-success', 'Данные обновлены.');
                    return $this->redirect(['me']);
                }
            }
        }

        return $this->render('me', [
            'model' => $model,
        ]);

    }

    public function actionDeletephoto()
    {

        $model = $this->findModel(Yii::$app->user->id);
        if ($model) {
            $model->deletePhoto();
            $model->save();
        }

        return $this->redirect(['me']);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \common\models\User|null|static
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            /** @var \common\models\User $model */
            $model->initial();
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
        }
    }

    /**
     * @param $service
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * Отвязывание аккаунтов соц.сетей от профиля
     */
    public function actionDisable($service)
    {
        $model = $this->findModel(Yii::$app->user->id);

        switch ($service) {
            case 'vkontakte':
                $model->oauth_vk_id = null;
                $model->vk_page = null;
                break;
            case 'odnoklassniki':
                $model->oauth_ok_id = null;
                $model->ok_page = null;
                break;
            case 'facebook':
                $model->oauth_fb_id = null;
                $model->fb_page = null;
                break;
        }
        $model->save();
        return $this->redirect(['/me']);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * Привзяывание аккаунтов соц.сетей к профилю
     */
    public function actionEnable()
    {
        $serviceName = Yii::$app->getRequest()->getQueryParam('service');
        if (isset($serviceName)) {
            User::loginByEAuth($serviceName, false);
        }
    }

    public function actionRepass()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('repass-success', 'На Email выслано письмо с дальнейшими инструкциями.');
            } else {
                Yii::$app->getSession()->setFlash('repass-error', 'Ошибка восстановления пароля. Обратитесь к администратору.');
            }
            return $this->redirect(['/repass']);
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }


    public function actionNewpass($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('newpass-success', 'Новый пароль установлен.');
            return $this->redirect(['/login']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
