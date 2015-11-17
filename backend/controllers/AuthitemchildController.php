<?php

namespace backend\controllers;

use Yii;
use common\models\AuthItemChild;
use backend\models\AuthItemChildSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * AuthItemChildController implements the CRUD actions for AuthItemChild model.
 */
class AuthitemchildController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Допуски > Просмотр таблицы'),
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Допуски > Создание'),
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Допуски > Редактирование'),
                    ],
                    [
                        'actions' => ['delete','multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Допуски > Удаление'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemChildSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AuthItemChild();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('permission-create-success');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('permission-update-success');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('permission-delete-success');

        return $this->redirect(['index']);
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
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
                Yii::$app->getSession()->setFlash('permission-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('permissions-delete-success');
            }
        }
        return true;
    }


    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItemChild the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItemChild::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
        }
    }
}
