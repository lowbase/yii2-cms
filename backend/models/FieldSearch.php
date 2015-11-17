<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Field;

/**
 * BoxSearch represents the model behind the search form about `common\models\Box`.
 */
class FieldSearch extends Field
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'position'], 'integer', 'on' => 'search'],
            [['option_id', 'value', 'document_id'], 'safe', 'on' => 'search'],
        ];
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
        $query = Field::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $this->load($params);

        if ($this->document_id) {
            $query->joinWith(['document' => function ($q) {
                $q->where(['like', 'document.name', $this->document_id])
                    ->orWhere(['like', 'document.id', $this->document_id]);
            }]);
        }
        if ($this->option_id) {
            $query->joinWith(['option' => function ($q) {
                $q->where(['like', 'option.name', $this->option_id])
                    ->orWhere(['like', 'option.id', $this->option_id]);
            }]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'position' => $this->position,
        ]);

        $query->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
