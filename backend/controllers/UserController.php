<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\AuthItemChild;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Пользователи > Просмотр таблицы'),
                    ],
                    [
                        'actions' => ['update', 'multiopen', 'multiclose'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Пользователи > Редактирование'),
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Пользователи > Удаление'),
                    ],
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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('user-update-success');
            return $this->redirect(['update', 'id' => $id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deletePhoto();
        $model->delete();
        Yii::$app->getSession()->setFlash('user-delete-success');
        return $this->redirect(['index']);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionMultidelete()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            $count = 0;
            foreach ($models as $id) {
                $this->findModel($id)->delete();
                $count ++;
            }
            if ($count == 1) {
                Yii::$app->getSession()->setFlash('user-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('users-delete-success');
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMultiopen()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            $count = 0;
            foreach ($models as $id) {
                $model = $this->findModel($id);
                $model->status = 1;
                $model->save();
                $count ++;
            }
            if ($count == 1) {
                Yii::$app->getSession()->setFlash('user-open-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('users-open-success');
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMulticlose()
    {
        $models = Yii::$app->request->post('keys');
        if ($models) {
            $count = 0;
            foreach ($models as $id) {
                $model = $this->findModel($id);
                $model->status = 0;
                $model->save();
                $count ++;
            }
            if ($count == 1) {
                Yii::$app->getSession()->setFlash('user-close-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('users-close-success');
            }
        }
        return true;
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            /** @var \common\models\User $model */
            $model->initial();
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найденыа.');
        }
    }
}
