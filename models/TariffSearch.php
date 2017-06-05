<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tariff;

/**
 * TariffSearch represents the model behind the search form about `app\models\Tariff`.
 */
class TariffSearch extends Tariff
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', /*'active',*/ ], 'integer'],
            [['cost'], 'number'],
            [['name', 'alias', 'created_at', 'updated_at'], 'safe'],
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
        $query = Tariff::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'cost', $this->cost])
            ->andFilterWhere(['like', 'alias', $this->alias]);

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

        return $dataProvider;
    }
}
