<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Option;

/**
 * TemplateSearch represents the model behind the search form about `common\models\Template`.
 */

class OptionSearch extends Option
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'require', 'multiple', 'template_id', 'id'], 'integer', 'on' =>'search'],
            [['name', 'param'], 'string', 'max' => 100, 'on' =>'search']
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

        $query = Option::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'template_id' => $this->template_id,
            'multiple' => $this->multiple,
            'require' => $this->require,
            'type' =>$this->type
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
