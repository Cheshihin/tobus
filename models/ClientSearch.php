<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;

/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    public $rating_from;
    public $rating_to;
    public $order_count_from;
    public $order_count_to;
    public $prize_trip_count_from;
    public $prize_trip_count_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', /*'last_point_from', 'last_point_to',*/ 'rating', 'order_count', 'prize_trip_count',], 'integer'],
            [['name', 'mobile_phone', 'home_phone', 'alt_phone', 'created_at', 'updated_at',
                'rating_from', 'rating_to', 'order_count_from', 'order_count_to', 'prize_trip_count_from', 'prize_trip_count_to'], 'safe'],
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
        $query = Client::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => (new PageSizeHelper([50, 100, 200]))->getRows()
//            ]
        ]);

//        $dataProvider->setSort([
//            'defaultOrder' => ['date' => SORT_DESC],
//        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'last_point_from' => $this->last_point_from,
//            'last_point_to' => $this->last_point_to,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'mobile_phone', $this->mobile_phone])
            ->andFilterWhere(['like', 'home_phone', $this->home_phone])
            ->andFilterWhere(['like', 'alt_phone', $this->alt_phone]);

        if (!empty($this->created_at)) {
            $created_at = strtotime($this->created_at);
            $query->andFilterWhere(['<', $this->tableName().'.created_at', $created_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.created_at', $created_at]);
        }
        if (!empty($this->updated_at)) {
            $updated_at = strtotime($this->updated_at);
            $query->andFilterWhere(['<', $this->tableName().'.updated_at', $updated_at + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.updated_at', $updated_at]);
        }

        if (!empty($this->rating_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.rating', $this->rating_from]);
        }
        if (!empty($this->rating_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.rating', $this->rating_to]);
        }

        if (!empty($this->order_count_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.order_count', $this->order_count_from]);
        }
        if (!empty($this->order_count_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.order_count', $this->order_count_to]);
        }

        if (!empty($this->prize_trip_count_from)) {
            $query->andFilterWhere(['>=', $this->tableName().'.prize_trip_count', $this->prize_trip_count_from]);
        }
        if (!empty($this->prize_trip_count_to)) {
            $query->andFilterWhere(['<=', $this->tableName().'.prize_trip_count', $this->prize_trip_count_to]);
        }

        return $dataProvider;
    }
}
