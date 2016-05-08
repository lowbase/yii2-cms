<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\document\controllers;

use app\admin\modules\document\models\DocumentSearch;
use app\admin\modules\document\models\Document;
use lowbase\document\models\Like;
use lowbase\document\models\Visit;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Документы (административная часть)
 * Class DocumentController
 * @package app\modules\back_document\controllers
 */
class DocumentController extends \lowbase\document\controllers\DocumentController
{
    public $layout = '@app/admin/layouts/main.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'multidelete', 'multiactive', 'multiblock', 'move', 'change', 'field'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['documentManager'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['documentView'],
                    ],
                    [
                        'actions' => ['create', 'change', 'field'],
                        'allow' => true,
                        'roles' => ['documentCreate'],
                    ],
                    [
                        'actions' => ['update', 'multiactive', 'multiblock', 'change', 'field', 'move'],
                        'allow' => true,
                        'roles' => ['documentUpdate'],
                    ],
                    [
                        'actions' => ['delete', 'multidelete'],
                        'allow' => true,
                        'roles' => ['documentDelete'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Менеджер документов (список таблицей)
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр карточки документа
     * @param $id - ID документа
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = Document::findOne($id);
        if ( $model== null) {
            throw new NotFoundHttpException(Yii::t('document', 'Запрашиваемая страница не найдена.'));
        }
        $views = Visit::getAll($model->id); // Считаем просмотры
        $likes = Like::getAll($model->id);  // Считаем лайки
        return $this->render('view', [
            'model' => $model,
            'views' => ($views) ?  $views[0]->count : 0,
            'likes' => ($likes) ?  $likes[0]->count : 0
        ]);
    }

}
