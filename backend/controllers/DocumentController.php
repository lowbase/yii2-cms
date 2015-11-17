<?php

namespace backend\controllers;

use common\models\Field;
use common\models\Option;
use Yii;
use common\models\Document;
use common\models\Template;
use backend\models\DocumentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\AuthItemChild;
use yii\web\UploadedFile;

/**
 * Class DocumentController
 * @package backend\controllers
 */
class DocumentController extends Controller
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
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Поиск по документам'),
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Создание'),
                    ],
                    [
                        'actions' => [
                            'update',
                            'multipublicate',
                            'multiclose',
                            'ajaxoptions',
                            'ajaxoption',
                            'deleteimg',
                            'deletefield'
                        ],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Редактирование'),
                    ],
                    [
                        'actions' => ['move'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Перемещение'),
                    ],
                    [
                        'actions' => [
                            'delete',
                            'multidelete'
                        ],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Удаление'),
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => AuthItemChild::getRolesByPermission('Администрирование: Документы > Предварительный просмотр'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Поиск по документам (таблица)
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Предварительный просмотр документа
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
     * Создание нового документа
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Document();
        $model->parent_id = Yii::$app->request->get('parent_id');
        $model->status = Document::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post())) {
            $model->injectValidate();
            $model = $this->loadAttributes($model);
            if ($model->validate()) {
                // Получаем будущего родителя
                $node = $this->findModel($model->parent_id);
                if ($model->appendTo($node)->save()) {
                    $model->savePhoto();
                }
                Yii::$app->getSession()->setFlash('document-create-success');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'template' => Template::findOne($model->template_id)
        ]);
    }

    /**
     * Редактирование документа
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->injectValidate();
            $model = $this->loadAttributes($model);
            if ($model->validate()) {
                // Документ не перемещается в новый документ
                if ($model->last_parent_id == $model->parent_id) {
                    if ($model->save()) {
                        $model->savePhoto();
                        Yii::$app->getSession()->setFlash('document-update-success');
                    }
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    // Документ сохраняется с перемещением
                    // Получаем будущего родителя
                    $parent = $this->findModel($model->parent_id);
                    if ($parent) {
                        $model->appendTo($parent)->save();
                        Yii::$app->getSession()->setFlash('document-update-move-success');
                        // Получаем прошлого родителя
                        /** @var \paulzi\nestedintervals\NestedIntervalsBehavior $last_parent */
                        $last_parent = $this->findModel($model->last_parent_id);
                        if ($last_parent) {
                            // Изменяем при необходимости значение "Папка?"
                            $descendants = $last_parent->getDescendants()->all();
                            if (!$descendants && $last_parent->is_folder) {
                                $last_parent->is_folder = 0;
                                /** @var \common\models\Document $last_parent */
                                $last_parent->save();
                            }
                        }
                    }
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'template' => Template::findOne($model->template_id)
        ]);

    }

    /**
     * Добавляем файлы и утраченные значения массива
     * fields после load модели
     * @param \common\models\Document $model
     * @return mixed
     */
    protected function loadAttributes($model)
    {
        /**
         * Добавление недостающих значений поля fields.
         * Т.к. после load затираются значения массива,
         * не пришедшие с POST-данными.
         * Устранение несовершенства функции SetAttribute Yii2.
         */
        $model->loadOptions();
        /**
         * Проверяем наличие файлов в расширенных
         * "быстрых" полях. Прикрепляем их к соответсвующим
         * аттрибутам.
         */
        $files = (isset($_FILES['Document']['name'])) ? array_keys($_FILES['Document']['name']) : null;
        if ($files) {
            foreach ($files as $file) {
                if (in_array($file, Template::getOptionArray('file'))) {
                    $model->$file = UploadedFile::getInstance($model, $file);
                }
            }
        }
        /**
         * Проверяем наличие файлов в дополнительных
         * полях. Прикрепляем их к соответствующим аттрибутам
         * (в соответствующее значение массива fields)
         */
        $field_files = (isset($_FILES['Document']['name']['fields'])) ? $_FILES['Document']['name']['fields'] : null;
        // Если пришли POST-данные дополнительных полей
        if ($field_files) {
            // Перебираем все дополнительные поля
            foreach ($field_files as $option_id => $option) {
                // Если пришел заполненный аттрибут
                // file дополнительного поля
                if (isset($option['file'])) {
                    // Значений может быть несколько
                    // (мультиполе), перебираем
                    foreach ($option['file'] as $field_id => $file) {
                        $model->fields[$option_id]['file'][$field_id] = UploadedFile::getInstance($model, 'fields['.$option_id.'][file]['.$field_id.']');
                    }
                }
            }
        }
        return $model;
    }

    /**
     * AJAX - перемещение документа на основании данных data
     * Приоритет данных для перемещения:
     * 1. Следующий документ
     * 2. Предыдущий документ
     * 3. Родительский документ
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionMove()
    {
        $data =Yii::$app->request->post();
        $model = $this->findModel($data['id']);
        $model->last_parent_id = $model->parent_id;
        $model->last_template_id = $model->template_id;
        /** @var \paulzi\nestedintervals\NestedIntervalsBehavior $model */
        $model->optimize();
        if ($model) {
            $model->parent_id = $data['new_parent_id'];

            if ($data['new_prev_id'] && $data['new_prev_id'] !== 'false') {
                $prev_model = $this->findModel($data['new_prev_id']);
            }
            /**
             * 1 Приоритет. Найден документ, после которого необходимо
             * произвести вставку (перемещение)
             */
            if (isset($prev_model) && $prev_model) {
                $model->insertAfter($prev_model)->save();
            } else {
                if ($data['new_next_id'] && $data['new_prev_id'] !== 'false') {
                    $next_model = $this->findModel($data['new_next_id']);
                }
                /**
                 * 2 Приоритет. Найден документ, перед которым необходимо
                 * произвести вставку (перемещение)
                 */
                if (isset($next_model) && $next_model) {
                    $model->insertBefore($next_model)->save();
                } else {
                    if ($data['new_parent_id'] && $data['new_parent_id'] !== 'false') {
                        $parent_model = $this->findModel($data['new_parent_id']);
                        /**
                         * 3 Приоритет. Не найдены соседи. Производим просто
                         * вставку в новый родительский документ
                         */
                        if ($parent_model) {
                            $model->prependTo($parent_model)->save();
                        }
                    }
                }
            }
            /**
             * Изменяем при необходимости значение "Папка?"
             * предыдущего родительского документа
             */
            /** @var \paulzi\nestedintervals\NestedIntervalsBehavior $last_parent */
            $last_parent = $this->findModel($data['old_parent_id']);
            if ($last_parent) {
                $descendants = $last_parent->getDescendants()->all();
                if (!$descendants && $last_parent->is_folder) {
                    $last_parent->is_folder = 0;
                    /** @var \common\models\Document $last_parent */
                    $last_parent->save();
                }
            }
        }

        return true;
    }

    /**
     * Отображение дополнительных полей
     * Используется при имземении шаблона
     * @return mixed
     */
    public function actionAjaxoptions()
    {
        $id = Yii::$app->request->post('id');
        if ($id) {
            $model = Document::findOne($id);
        } else {
            $model = new Document();
        }
        /**
         * Не используем функцию findModel, т.к.
         * в данном случае важно изменение шаблона
         * перед инициализацией для установления
         * новых дополнительных полей, соотвествующих
         * новому выбранному шаблону
         */
        $model->last_parent_id = $model->parent_id;
        $model->last_template_id = $model->template_id;
        $model->template_id = Yii::$app->request->post('template_id');
        $model->initialization();
        $template = Template::findOne($model->template_id);
        $empty_value = ($model->last_template_id != $model->template_id) ? true : false;

        return $this->renderAjax('_options_fields', [
            'model' => $model,
            'template' => $template,
            'empty_value' => $empty_value
        ]);
    }

    /**
     * Отображение дополнительного поля
     * Используется при добавлении значения
     * мультиполя
     * @return mixed
     */
    public function actionAjaxoption()
    {
        $id = Yii::$app->request->post('id');
        $new_field = Yii::$app->request->post('newfield');
        $option = Option::findOne($id);

        return $this->renderAjax('_field', [
            'option' => $option,
            'option_id' => $id,
            'field_id' => 'multi_new_' . $new_field,
        ]);
    }

    /**
     * Удаление значения мультиполя
     * @return mixed
     */
    public function actionDeletefield()
    {
        $field_id = Yii::$app->request->get('id');
        /** @var \common\models\Field $field */
        $field = Field::findOne($field_id);
        if ($field && isset($field->option)) {
            //необходимое кол-во значений мультиполя
            $count_require = $field->option->require;
            if ($count_require) {
                $all_fields = Field::find()->where([
                    'option_id' => $field->option_id,
                    'document_id' => $field->document_id
                ])->all();
                if (count($all_fields) <= $count_require) {
                    return $this->redirect(['update', 'id' => $field->document_id]);
                }
            }
            $field->deletePhoto();
            $document_id = $field->document_id;
            $field->delete();
            Yii::$app->getSession()->setFlash('field-delete-success');
            return $this->redirect(['update', 'id' => $document_id]);
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    /**
     * Удаление изображений "быстрых" полей
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDeleteimg()
    {
        $document_id = Yii::$app->request->get('document_id');
        $option_id = Yii::$app->request->get('option_id');

        if ($document_id && $option_id) {
            $model = $this->findModel($document_id);
            $model->deletePhoto($option_id);
            $model->save();
            Yii::$app->getSession()->setFlash('image-delete-success');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    /**
     * Активация нескольких документов единовременно.
     * @return bool
     */
    public function actionMultipublicate()
    {
        $keys = Yii::$app->request->post('keys');
        if ($keys) {
            $db = Document::getDb();
            $db->createCommand()->update('document', ['status' => 1], [
                'id' => $keys,
                'status' => 0])->execute();
        }
        Yii::$app->getSession()->setFlash('documents-publicate-success');
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
            $db = Document::getDb();
            $db->createCommand()->update('document', ['status' => 0], [
                'id' => $keys,
                'status' => [1,2]])->execute();
        }
        Yii::$app->getSession()->setFlash('documents-close-success');
        return true;
    }

    /**
     * Удаление документа
     * @param $id
     * @param $from_multi
     * @return bool
     * @throws NotFoundHttpException
     */
    public function actionDelete($id, $from_home = false)
    {
        $model = $this->findModel($id);
        $parent = $model->parent;
        //Удаляем документ со всеми дочерними документами
        /** @var \paulzi\nestedintervals\NestedIntervalsBehavior $model */

        $model->deleteWithChildren();
        /**
         * Изменяем при необходимости значение "Папка?"
         * предыдущего родительского документа
         */
        $descendants = $parent->getDescendants()->all();
        if (!$descendants && $parent->is_folder) {
            $parent->is_folder = 0;
            $parent->save();
        }
        Yii::$app->getSession()->setFlash('document-delete-success');
        return (Yii::$app->request->isAjax) ? true : ($from_home) ? $this->redirect(['/']) : $this->redirect(['index']);
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
            foreach ($models as $id) {
                $this->actionDelete($id, true);
            }
        }
        Yii::$app->getSession()->setFlash('documents-delete-success');
        return true;
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var \common\models\Document $model */
        $model = Document::findOne($id);
        if ($model !== null) {
            $model->last_parent_id = $model->parent_id;
            $model->last_template_id = $model->template_id;
            $model->initialization();
            return $model;
        } else {
            throw new NotFoundHttpException('Страница не найдена.');
        }
    }
}
