<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Message;
use common\helpers\CFF;

/**
 * MessageSearch represents the model behind the search form about `common\models\Message`.
 */
class MessageSearch extends Message
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
        for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
            $options[] = 'option_' . $i;
        }

        $rules[] = [['id', 'id_from', 'id_till', 'status', 'created_user_id', 'updated_user_id',
            'parent_message_id'], 'integer', 'on' => 'search'];
        $rules[] = [array_merge($options, ['title', 'content', 'attachment',
            'created_at', 'created_at_from', 'created_at_till', 'updated_at_from', 'updated_at_till',
            'for_document_id', 'for_user_id', 'updated_at',
            'created_user_name', 'updated_user_name', 'created_ip']), 'safe', 'on' => 'search'];

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
        $query = Message::find();

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'for_document_id' => $this->for_document_id,
            'for_user_id' => $this->for_user_id,
            'parent_message_id' => $this->parent_message_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'attachment', $this->attachment])
            ->andFilterWhere(['like', 'created_user_name', $this->created_user_name])
            ->andFilterWhere(['like', 'updated_user_name', $this->updated_user_name])
            ->andFilterWhere(['like', 'created_ip', $this->created_ip])
            ->andFilterWhere(['>=', 'id', $this->id_from])
            ->andFilterWhere(['<=', 'id', $this->id_till]);

        for ($i = 1; $i <= Message::OPTIONS_COUNT; $i++) {
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
