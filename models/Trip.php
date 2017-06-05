<?php

namespace app\models;

use Yii;
use app\models\Direction;
use app\models\Order;
use app\models\TripStatic;
use yii\helpers\ArrayHelper;
use app\models\Transport;

/**
 * Рейсы
 *
 *  !чтение данных по рейсам не должно происходить напрямую через Trip::find(), а должно происходить
 *  только через функции текущей модели
 *
 * @property integer $id
 * @property string $name
 * @property integer $date
 * @property integer $direction_id
 * @property string $start_time
 * @property string $mid_time
 * @property string $end_time
 * @property integer $sent_date
 * @property integer $created_at
 * @property integer $updated_at
 */
class Trip extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'direction_id', 'sent_date', 'transport_id', 'created_at', 'updated_at'], 'integer'],
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
            'date' => 'Дата',
            'direction_id' => 'Направление',
            'start_time' => 'Начало сбора',
            'mid_time' => 'Середина сбора',
            'end_time' => 'Конец сбора',
            'sent_date' => 'Дата отправки',
            'transport_id' => 'Транспорт',
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
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasOne(Order::className(), ['trip_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransport()
    {
        return $this->hasOne(Transport::className(), ['transport_id' => 'id']);
    }



    /*
     * Функция проверяет рейсы на запрашиваемую дату и генерирует при необходимости рейсы
     *
     * @param $date - int unixtime
     * @param query_params array - массив пар: переменная модели - значение модели
     */
    public static function checkGenerateTrips($unixdate)
    {
        $unixdate = intval($unixdate);
        $correct_unixdate = strtotime(date('d.m.Y', $unixdate));

        $unixtoday = strtotime(date('d.m.Y'));

        // проверяю существование хоть одной записи на дату $date. И в случае ее отсутствия создаю
        //  записи на основе trip_static (только для сегодня и для будущих дней )
        if($unixdate >= $unixtoday) {

            // $query->andFilterWhere(['<', $this->tableName().'.date', $date + 86400]);
            $trip = Trip::find()
                ->where(['>=', 'date', $correct_unixdate])
                ->andWhere(['<', 'date', $correct_unixdate + 86400])
                ->one();

            if($trip == null) {
                Trip::createStandartTripList($correct_unixdate);
            }
        }

        return true;
    }

    /*
     * Функция создания списка рейсов для "свободного" дня на основе расписания рейсов из trip_static
     *
     * @param $unixdate string - указывает на 0 часов, 0 минут и 0 секунд какого либо дня
     */
    public static function createStandartTripList($unixdate)
    {
        $trip_static_list = TripStatic::find()->all();

        // копируем: name, direction_id, start_time, mid_time, end_time,
        // отдельно создаются: date, created_at
        // пропускаю: sent_date = null, updated_at

        $trips = [];
        foreach($trip_static_list as $trip_static) {
            $trip = new Trip();
            $trip->name = $trip_static->name;
            $trip->direction_id = $trip_static->direction_id;
            $trip->start_time = $trip_static->start_time;
            $trip->mid_time = $trip_static->mid_time;
            $trip->end_time = $trip_static->end_time;

            // получаем дату+время в формате unixtime
            $trip->date = strtotime(date('d.m.Y', $unixdate).' '.$trip_static->mid_time);
            $trip->created_at = time();

            $trips[] = $trip;
        }


        $rows = ArrayHelper::getColumn($trips, 'attributes');


        return Yii::$app->db->createCommand()->batchInsert(Trip::tableName(), $trip->attributes(), $rows)->execute();
    }
}
