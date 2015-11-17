<?php

namespace backend\controllers;

use Yii;
use backend\models\FieldSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\AuthItemChild;

/**
 * BoxController implements the CRUD actions for Box model.
 */
class FieldController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Дополнительные поля > Поиск'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Box models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FieldSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}

