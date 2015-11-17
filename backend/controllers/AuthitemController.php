<?php

namespace backend\controllers;

use Yii;
use common\models\AuthItem;
use backend\models\AuthItemSearch;
use common\models\AuthItemChild;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthitemController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Роли > Просмотр таблицы'),
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Роли > Создание'),
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Роли > Редактирование'),
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Роли > Удаление'),
                    ],
                ],
            ],
        ];
    }
    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->type == 1) {
                Yii::$app->getSession()->setFlash('role-create-success');
            } elseif ($model->type == 2) {
                Yii::$app->getSession()->setFlash('rights-create-success');
            }
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->type == 1) {
                Yii::$app->getSession()->setFlash('role-update-success');
            } elseif ($model->type == 2) {
                Yii::$app->getSession()->setFlash('rights-update-success');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model =$this->findModel($id);
        if ($model->type == 1) {
            Yii::$app->getSession()->setFlash('role-delete-success');
        } elseif ($model->type == 2) {
            Yii::$app->getSession()->setFlash('rights-delete-success');
        }
        $model->delete();

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
                Yii::$app->getSession()->setFlash('role-rights-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('roles-rights-delete-success');
            }
        }
        return true;
    }

   /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
        }
    }
}
