<?php

namespace backend\controllers;

use Yii;
use common\models\AuthItemChild;
use common\models\Option;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * OptionController implements the CRUD actions for Option model.
 */
class OptionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Шаблоны > Редактирование'),
                    ]
                ],
            ],
        ];
    }


    /**
     * Creates a new Option model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Option();

        $model->template_id = Yii::$app->request->get('template_id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('option-create-success');
            return $this->redirect(['/template/update', 'id' => $model->template_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Option model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('option-update-success');
            return $this->redirect(['/template/update', 'id' => $model->template_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Option model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $template_id = $model->template_id;
        $model->delete();
        Yii::$app->getSession()->setFlash('option-delete-success');

        return $this->redirect(['/template/update', 'id' => $template_id]);
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
                Yii::$app->getSession()->setFlash('option-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('options-delete-success');
            }
        }

        return true;
    }


    /**
     * Finds the Option model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param Option $id
     * @return Option the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Option::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
