<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Document;
use common\models\Template;
use common\helpers\CFF;

/**
 * DocumentSearch represents the model behind the search form about `common\models\Document`.
 */
class DocumentSearch extends Document
{
    public $id_from;
    public $id_till;
    public $created_at_from;
    public $created_at_till;
    public $updated_at_from;
    public $updated_at_till;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $options = [];
        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $options[] = 'option_' . $i;
        }

        $rules[] = [['id', 'id_from', 'id_till', 'lft', 'rgt', 'depth',
            'updated_user_id', 'created_user_id','template_id','status',
            'is_folder','root_id','parent_id'], 'integer', 'on'=>'search'];
        $rules[] = [array_merge($options, ['name','created_at', 'created_at_from','created_at_till',
            'updated_at_from','updated_at_till', 'updated_at', 'created_user_name',
            'updated_user_name', 'title', 'alias', 'annotation', 'content', 'img',
            'meta_description', 'meta_keywords','root_name','parent_name']), 'safe','on'=>'search'];

        return $rules;
    }

    public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['id_from'] = 'ID (от)';
        $attr['id_till'] = 'ID (до)';
        $attr['created_at_from'] = 'Создан (от)';
        $attr['created_at_till'] = 'Создан (до)';
        $attr['updated_at_from'] = 'Редактирован (от)';
        $attr['updated_at_till'] = 'Редактирован (до)';
        return $attr;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->scenario = 'search';
        $query = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'lft' => $this->lft,
            'rgt' => $this->rgt,
            'depth' => $this->depth,
            'template_id' => $this->template_id,
            'is_folder' => $this->is_folder,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'created_user_name', $this->created_user_name])
            ->andFilterWhere(['like', 'updated_user_name', $this->updated_user_name])
            ->andFilterWhere(['like', 'root_name', $this->root_name])
            ->andFilterWhere(['like', 'parent_name', $this->parent_name])
            ->andFilterWhere(['like', 'created_at', CFF::FormatData($this->created_at)])
            ->andFilterWhere(['like', 'updated_at', CFF::FormatData($this->updated_at)])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'meta_description', $this->meta_description])
            ->andFilterWhere(['like', 'meta_keywords', $this->meta_keywords])
            ->andFilterWhere(['>=', 'id', $this->id_from])
            ->andFilterWhere(['<=', 'id', $this->id_till])
            ->andFilterWhere(['<>', 'id', 1]);

        for ($i = 1; $i <= Template::OPTIONS_COUNT; $i++) {
            $option = 'option_' . $i;
            $query->andFilterWhere(['like', $option, $this->$option]);
        }

        if ($this->created_at_from) {
            $query->andFilterWhere(['>=', 'created_at', CFF::FormatData($this->created_at_from, false) . ' 00:00:00']);
        }
        if ($this->created_at_till) {
            $query->andFilterWhere(['<=', 'created_at', CFF::FormatData($this->created_at_till, false).' 23:59:00']);
        }
        if ($this->updated_at_from) {
            $query->andFilterWhere(['>=', 'updated_at', CFF::FormatData($this->updated_at_from, false).' 00:00:00']);
        }
        if ($this->updated_at_till) {
            $query->andFilterWhere(['<=', 'updated_at', CFF::FormatData($this->updated_at_till, false).' 23:59:00']);
        }

        return $dataProvider;
    }
}
