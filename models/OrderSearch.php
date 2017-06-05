<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use app\models\Client;
use app\models\Trip;

/**
 * OrderSearch represents the model behind the search form about `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'direction_id', 'status_id', 'tr_id', 'point_id_from', 'point_id_to', 'is_free',
                'places_count', 'student_count', 'child_count', 'bag_count', 'suitcase_count', 'oversized_count',
                'is_not_places', 'parent_id', 'categ_id', 'informer_office_id'], 'integer'],
            [['price'], 'number'],
            [[/*'alt_fio',*/ 'comment', 'additional_phone_1', 'additional_phone_2', 'additional_phone_3',
                'date', 'time_getting_into_car', 'time_confirm', 'time_sat', 'created_at',
                'updated_at', 'client_id', 'trip_id', /*'baggage'*/ ], 'safe'],
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
     * Поиск для таблицы в админке
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()->joinWith(['client', 'trip']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['date' => SORT_DESC],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 'date', 'time_getting_into_car', 'time_confirm', 'time_sat', 'created_at', 'updated_at'

        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.tr_id' => $this->tr_id,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.is_free' => $this->is_free,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            //$this->tableName().'.baggage' => $this->baggage,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.parent_id' => $this->parent_id,
            $this->tableName().'.categ_id' => $this->categ_id,
            $this->tableName().'.price' => $this->price,
        ]);

        $query
            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            ->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if (!empty($this->date)) {
            $date = strtotime($this->date);
            $query->andFilterWhere(['<', $this->tableName().'.date', $date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $date]);
        }
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

        if (!empty($this->time_getting_into_car)) {
            $time_getting_into_car = strtotime($this->time_getting_into_car);
            $query->andFilterWhere(['<', $this->tableName().'.time_getting_into_car', $time_getting_into_car + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_getting_into_car', $time_getting_into_car]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        return $dataProvider;
    }

    /**
     * Поиск для таблицы в админке
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function TripSearch($params, $trip_id)
    {
        $query = Order::find()->joinWith(['client', 'trip']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100
            ]
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['date' => SORT_DESC],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 'date', 'time_getting_into_car', 'time_confirm', 'time_sat', 'created_at', 'updated_at'

        // grid filtering conditions
        $query->andFilterWhere([
            $this->tableName().'.id' => $this->id,
            $this->tableName().'.trip_id' => $trip_id,
            $this->tableName().'.status_id' => $this->status_id,
            $this->tableName().'.direction_id' => $this->direction_id,
            $this->tableName().'.tr_id' => $this->tr_id,
            $this->tableName().'.point_id_from' => $this->point_id_from,
            $this->tableName().'.point_id_to' => $this->point_id_to,
            $this->tableName().'.is_free' => $this->is_free,
            $this->tableName().'.informer_office_id' => $this->informer_office_id,
            $this->tableName().'.places_count' => $this->places_count,
            $this->tableName().'.student_count' => $this->student_count,
            $this->tableName().'.child_count' => $this->child_count,
            $this->tableName().'.bag_count' => $this->bag_count,
            $this->tableName().'.suitcase_count' => $this->suitcase_count,
            $this->tableName().'.oversized_count' => $this->oversized_count,
            //$this->tableName().'.baggage' => $this->baggage,
            $this->tableName().'.is_not_places' => $this->is_not_places,
            $this->tableName().'.parent_id' => $this->parent_id,
            $this->tableName().'.categ_id' => $this->categ_id,
            $this->tableName().'.price' => $this->price,
        ]);

        $query
            ->andFilterWhere(['like', $this->tableName().'.comment', $this->comment])
            ->andFilterWhere(['like', Client::tableName().'.name', $this->client_id])
            //->andFilterWhere(['like', Trip::tableName().'.name', $this->trip_id])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_1', $this->additional_phone_1])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_2', $this->additional_phone_2])
            ->andFilterWhere(['like', $this->tableName().'.additional_phone_3', $this->additional_phone_3]);

        if (!empty($this->date)) {
            $date = strtotime($this->date);
            $query->andFilterWhere(['<', $this->tableName().'.date', $date + 86400]);
            $query->andFilterWhere(['>=', $this->tableName().'.date', $date]);
        }
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

        if (!empty($this->time_getting_into_car)) {
            $time_getting_into_car = strtotime($this->time_getting_into_car);
            $query->andFilterWhere(['<', $this->tableName().'.time_getting_into_car', $time_getting_into_car + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_getting_into_car', $time_getting_into_car]);
        }

        if (!empty($this->time_confirm)) {
            $time_confirm = strtotime($this->time_confirm);
            $query->andFilterWhere(['<', $this->tableName().'.time_confirm', $time_confirm + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_confirm', $time_confirm]);
        }

        if (!empty($this->time_sat)) {
            $time_sat = strtotime($this->time_sat);
            $query->andFilterWhere(['<', $this->tableName().'.time_sat', $time_sat + 60]);
            $query->andFilterWhere(['>=', $this->tableName().'.time_sat', $time_sat]);
        }

        return $dataProvider;
    }
}
