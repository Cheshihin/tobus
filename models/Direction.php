<?php

namespace app\models;

use Yii;
use app\models\City;
use app\models\Trip;

/**
 * This is the model class for table "direction".
 *
 * @property integer $id
 * @property string $sh_name
 * @property integer $city_from
 * @property integer $city_to
 * @property integer $distance
 * @property integer $created_at
 * @property integer $updated_at
 */
class Direction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'direction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sh_name'], 'required'],
            [['city_from', 'city_to', 'distance', 'created_at', 'updated_at'], 'integer'],
            [['sh_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sh_name' => 'Краткое название',
            'city_from' => 'Город отправления',
            'city_to' => 'Город назначения',
            'distance' => 'Дистанция, км',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityFrom()
    {
        return $this->hasOne(City::className(), ['id' => 'city_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityTo()
    {
        return $this->hasOne(City::className(), ['id' => 'city_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrips()
    {
        return $this->hasMany(Trip::className(), ['direction_id' => 'id']);
    }

}
