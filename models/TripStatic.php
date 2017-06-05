<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trip_static".
 *
 * @property integer $id
 * @property string $name
 * @property integer $direction_id
 * @property string $start_time
 * @property string $mid_time
 * @property string $end_time
 * @property integer $created_at
 * @property integer $updated_at
 */
class TripStatic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trip_static';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direction_id', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['start_time', 'mid_time', 'end_time'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'direction_id' => 'Направление',
            'start_time' => 'Начало сбора',
            'mid_time' => 'Середина сбора',
            'end_time' => 'Конец сбора',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
        ];
    }
}
