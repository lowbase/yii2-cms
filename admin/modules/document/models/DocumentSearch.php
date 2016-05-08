<?php
/**
 * @package   yii2-cms
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

namespace app\admin\modules\document\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Поиск среди документов с учетом пользователей
 * Class DocumentSearch
 * @package lowbase\document\models
 */
class DocumentSearch extends \app\admin\modules\document\models\Document
{
    const COUNT = 50; // количество документов на одной странице

    public $id_from;        // Начало диапазона поиска по ID
    public $id_till;        // Конец диапазона поиска по ID
    public $position_from;  // Начало диапазона поиска по позиции
    public $position_till;  // Конец диапазона поиска по позиции
    public $created_at_from;// Начало диапазона поиска по дате создания
    public $created_at_till;// Конец диапазона поиска по дате создания
    public $updated_at_from;// Начало диапазона поиска по дате редактирования
    public $updated_at_till;// Конец диапазона поиска по дате редактирования

    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'position', 'id_from', 'id_till', 'position_from',
                'position_till', 'status', 'is_folder', 'parent_id', 'template_id',
                'created_by', 'updated_by'], 'integer', 'on' => 'search'],  // Целочисленные значения
            [['name', 'alias', 'title', 'meta_keywords', 'meta_description',
                'annotation', 'content', 'image', 'created_at', 'created_at_from',
                'created_at_till',  'updated_at_from', 'updated_at_till',
                'updated_at'], 'safe', 'on' => 'search'],   // Безопасные аттрибуты
        ];
    }


    /**
     * Сценарии
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Названия дополнительных полей
     * поиска документов
     * @return array
     */
    public function attributeLabels()
    {
        $label = parent::attributeLabels();
        $label['id_from'] = Yii::t('document', 'От Id');
        $label['id_till'] = Yii::t('document', 'До Id');
        $label['position_from'] = Yii::t('document', 'От позиции');
        $label['position_till'] = Yii::t('document', 'До позиции');
        $label['created_at_from'] = Yii::t('document', 'Создан с');
        $label['created_at_till'] = Yii::t('document', 'Создан до');
        $label['updated_at_from'] = Yii::t('document', 'Редактирован с');
        $label['updated_at_till'] = Yii::t('document', 'Редактирован до');
        return $label;
    }

    /**
     * Создает DataProvider на основе переданных данных
     * @param $params - параметры
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->scenario = 'search'; // Устанавливаем сценарий поиска
        $query = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'=> $this::COUNT,
            ],
            'sort' => array(
                'defaultOrder' => ['created_at' => SORT_DESC],
            ),
        ]);

        $this->load($params);

        // Если валидация не пройдена, то ничего не выводить
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        
        // Фильтрация
        $query->andFilterWhere([
            'id' => $this->id,
            'position' => $this->position,
            'status' => $this->status,
            'is_folder' => $this->is_folder,
            'parent_id' => $this->parent_id,
            'template_id' => $this->template_id,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        if ($this->created_at) {
            $date = new \DateTime($this->created_at);
            $this->created_at = $date->format('Y-m-d');
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'meta_keywords', $this->meta_keywords])
            ->andFilterWhere(['like', 'meta_description', $this->meta_description])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'created_at', $this->created_at]);
        
        if ($this->id_from){
            $query->andFilterWhere(['>=', 'id', $this->id_from]);
        }
        if ($this->id_till){
            $query->andFilterWhere(['<=', 'id', $this->id_till]);
        }
        if ($this->position_from){
            $query->andFilterWhere(['>=', 'position', $this->position_from]);
        }
        if ($this->position_till){
            $query->andFilterWhere(['<=', 'position', $this->position_till]);
        }
        if ($this->created_at_from) {
            $date_from = new \DateTime($this->created_at_from);
            $query->andFilterWhere(['>=', 'created_at', $date_from->format('Y-m-d')]);
        }
        if ($this->created_at_till) {
            $date_till = new \DateTime($this->created_at_till);
            $query->andFilterWhere(['<=', 'created_at', $date_till->format('Y-m-d')]);
        }
        if ($this->updated_at_from) {
            $date_from = new \DateTime($this->updated_at_from);
            $query->andFilterWhere(['>=', 'updated_at', $date_from->format('Y-m-d')]);
        }
        if ($this->updated_at_till) {
            $date_till = new \DateTime($this->updated_at_till);
            $query->andFilterWhere(['<=', 'updated_at', $date_till->format('Y-m-d')]);
        }

        return $dataProvider;
    }
}
