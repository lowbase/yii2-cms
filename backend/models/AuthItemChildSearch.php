<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AuthItemChild;

/**
 * AuthItemChildSearch represents the model behind the search form about `common\models\AuthItemChild`.
 */
class AuthItemChildSearch extends AuthItemChild
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'safe', 'on' => 'search'],
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

        $query = AuthItemChild::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>array(
                'pageSize'=>100,
            ),
          ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'parent', $this->parent])
            ->andFilterWhere(['like', 'child', $this->child]);

        return $dataProvider;
    }
}
