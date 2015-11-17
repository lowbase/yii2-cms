<?php

namespace backend\controllers;

use Yii;
use common\models\Message;
use backend\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\AuthItemChild;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Сообщения > Просмотр таблицы'),
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Сообщения > Создание'),
                    ],
                    [
                        'actions' => [
                            'update',
                            'multipublicate',
                            'multiclose',
                            'deleteattachment'
                        ],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Сообщения > Редактирование'),
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Сообщения > Удаление'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

     /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();
        $model->status = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('message-create-success');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('message-update-success');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('message-delete-success');

        return $this->redirect(['index']);
    }

    /**
     * Активация нескольких документов единовременно.
     * @return bool
     */
    public function actionMultipublicate()
    {
        $keys = Yii::$app->request->post('keys');
        if ($keys) {
            $db = Message::getDb();
            $db->createCommand()->update('message', ['status' => 1], [
                'id' => $keys,
                'status' => 0])->execute();
            Yii::$app->getSession()->setFlash('message-publicate-success');
        }
        return true;
    }

    /**
     * Деактивация нескольких документов единовременно.
     * @return bool
     */
    public function actionMulticlose()
    {
        $keys = Yii::$app->request->post('keys');
        if ($keys) {
            $db = Message::getDb();
            $db->createCommand()->update('message', ['status' => 0], [
                'id' => $keys,
                'status' => [1,2]])->execute();
            Yii::$app->getSession()->setFlash('message-close-success');
        }
//        return true;
    }

    /**
     * Удаление нескольких документов единовременно
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
                Yii::$app->getSession()->setFlash('message-delete-success');
            } elseif ($count > 1) {
                Yii::$app->getSession()->setFlash('messages-delete-success');
            }
        }
        return true;
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
