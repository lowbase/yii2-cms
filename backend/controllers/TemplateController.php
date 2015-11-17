<?php

namespace backend\controllers;

use common\models\Option;
use Yii;
use common\models\Template;
use backend\models\TemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\OptionSearch;
use common\models\AuthItemChild;
use yii\filters\AccessControl;

/**
 * TemplateController implements the CRUD actions for Template model.
 */
class TemplateController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Шаблоны > Просмотр таблицы'),
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Шаблоны > Создание'),
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Шаблоны > Редактирование'),
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Шаблоны > Удаление'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Template models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Template model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Template();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('template-create-success');
            return $this->redirect(['/template/update', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Template model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $searchOption = new OptionSearch();
        $searchOption->template_id = $model->id;
        $dataProvider = $searchOption->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('template-update-success');
            return $this->redirect(['/template/update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'searchModel' => $searchOption,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Template model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Option::deleteAll(['template_id' => $id]);
        $model->delete();
        Yii::$app->getSession()->setFlash('template-delete-success');
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
                Yii::$app->getSession()->setFlash('template-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('templates-delete-success');
            }
        }

        return true;
    }

    /**
     * Finds the Template model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Template the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Template::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
