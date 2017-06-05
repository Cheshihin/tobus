<?php

namespace app\models;

use Yii;
use app\models\OrderStatus;
use app\models\Client;
use app\models\Tariff;
use app\models\Point;
use app\models\Trip;
use app\models\Direction;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $status_id
 * @property integer $date
 * @property integer $client_id
 * @property integer $tr_id
 * @property integer $point_id_from
 * @property integer $point_id_to
 * @property integer $is_free
 * @property integer $trip_id
 * @property integer $places_count
 * @property integer $student_count
 * @property integer $child_count
 * @property integer $baggage
 * @property integer $is_not_places
 * @property integer $parent_id
 * @property integer $time_getting_into_car
 * @property string $comment
 * @property string $additional_phone_1
 * @property string additional_phone_2
 * @property string additional_phone_3
 * @property integer $time_confirm
 * @property integer $categ_id
 * @property integer $time_sat
 * @property integer $use_fix_price
 * @property string $price
 * @property integer $created_at
 * @property integer $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    const LOYALITY = 5; //  лояльность. Какая поездка в текущей тарифной сетке является призовой

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'direction_id', 'trip_id', 'point_id_from', 'point_id_to', 'radio_group_2',
                'first_writedown_click_time', 'first_writedown_clicker_id', 'first_confirm_click_time',
                'first_confirm_clicker_id',
                'radio_group_1', 'radio_group_2', 'radio_group_3'], 'required'],

            [['places_count'], 'checkPlacesCount', 'skipOnEmpty' => false],
            [['point_id_from'], 'timeArrivalCheck'],
            [['point_id_to'], 'timeDepartureCheck'],
            ['time_getting_into_car', 'timeGettingIntoCarCheck', 'skipOnEmpty' => false],

            [['direction_id', 'status_id', 'tr_id', 'is_free', 'trip_id',
                'is_not_places', 'places_count', 'student_count', 'child_count', 'bag_count', 'suitcase_count', 'oversized_count',
                'parent_id', 'categ_id', 'informer_office_id',
                'point_id_from', 'point_id_to', 'prize_trip_count',
                'first_writedown_click_time', 'first_writedown_clicker_id', 'first_confirm_click_time', 'first_confirm_clicker_id',
                'radio_group_1', 'radio_group_2', 'radio_group_3'
            ], 'integer'],
            [['radio_group_2'], 'number', 'min' => 1],
            //[['price',], 'number'],
            [['additional_phone_1', 'additional_phone_2', 'additional_phone_3'], 'string', 'max' => 20],
            /*[['alt_fio'], 'string', 'max' => 100],*/
            [['comment', ], 'string', 'max' => 255],
            [['date'], 'checkDate'],
            [['time_getting_into_car', 'time_confirm', 'time_sat', 'created_at', 'updated_at', 'client_id',
                'time_air_train_arrival', 'time_air_train_departure', 'use_fix_price','price'], 'safe'],
        ];
    }

    /*
     * Проверка уникальности номера карты
     */
    public function checkDate($attribute, $params)
    {
        if(isset($this->date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->date = strtotime($this->date);   // convent '07.11.2016' to unixtime
        }

        $today_finish = strtotime(date('d.m.Y'));
        if($this->date < $today_finish) {
            $this->addError($attribute, 'Нельзя выбрать прошедшую дату');
        }else {
            return true;
        }
    }

    /*
     * Функция проверки времени предполагаемой посадки в машину
     */
    public function timeGettingIntoCarCheck($attribute, $params)
    {
        // пример: 12 : 00
        if(empty($this->time_getting_into_car)) {
            $this->addError($attribute, 'Необходимо заполнить «Время подтверждения транспорта».');
        }elseif(isset($this->time_getting_into_car) && preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_getting_into_car))
        {
            if(isset($this->date) && !empty($this->date) && isset($this->trip_id) && $this->trip_id > 0) {

                if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                    $this->date = strtotime($this->date);
                }

                $trip = $this->trip;
                //$start_time = strtotime(date("d.m.Y ", $this->date) . $trip->start_time) - 1800;
                //$end_time = strtotime(date("d.m.Y ", $this->date) . $trip->end_time) + 7200;

                $start_time = strtotime($trip->start_time) - 1800;
                $end_time = strtotime($trip->end_time) + 7200;

                $arTimeIntoCar = explode(':', $this->time_getting_into_car);
                $time_getting_into_car = strtotime(trim($arTimeIntoCar[0]).':'.trim($arTimeIntoCar[1]));
                if(($time_getting_into_car >= $start_time) && ($time_getting_into_car <= $end_time)) {
                    return true;
                }else {
                    $this->addError($attribute, 'Время подтверждения должно находиться в диапозоне от '.date('H:i', $start_time).' до '.date('H:i', $end_time));
                }

            }else {
                $this->addError($attribute, 'Для формирования времени подтверждения транспорта должны быть заполнены поле «Дата» и выбран «Рейс».');
            }

        }else {
            $this->addError($attribute, 'Формат времени подтверждения транспорта неверен.');
        }

        return true;
    }

    /*
     * Проверка количества мест
     */
    public function checkPlacesCount($attribute, $params)
    {
        if($this->is_not_places == 1) {
            return true;
        }

        if(empty($this->places_count) || $this->places_count == 0) {
            $this->addError($attribute, 'Необходимо заполнить «Мест».');
        }
    }

    /*
     * Проверка времени отправления поезда/самолета
     */
    public function timeArrivalCheck($attribute, $params)
    {
        if(!empty($this->point_id_from)) {
            $point_from = $this->pointFrom;
            if($point_from != null && $point_from->critical_point == 1 && trim($this->time_air_train_arrival) == '') {
                $this->addError($attribute, 'Необходимо заполнить «Время прибытия поезда/авиарейса».');
            }
        }
    }

    /*
     * Проверка времени прибытия поезда/самолета на правильность
     */
    public function timeDepartureCheck($attribute, $params)
    {
        if(!empty($this->point_id_to)) {
            $point_to = $this->pointTo;
            if($point_to != null && $point_to->critical_point == 1 && trim($this->time_air_train_departure) == '') {
                $this->addError($attribute, 'Необходимо заполнить «Время отправления поезда/авиарейса».');
            }
        }
    }


    public function getRadioGroup1() {
        $list = [
            1 => 'Будьте собраны и готовы в {ВРПТ}, без звонка не выходите',
            2 => 'Вам нужно быть на {ТЧК_ОТКУДА} в {ВРПТ}, подъедет машина номер ___'
        ];

        if(!empty($this->time_getting_into_car)) {

            if(preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_getting_into_car)) {
                $hour_minute = explode(':', $this->time_getting_into_car);

                if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                    $this->date = strtotime($this->date);
                }


                $this->time_getting_into_car = $this->date + trim($hour_minute[0])*3600+ trim($hour_minute[1])*60;
            }

            $list[1] = str_replace('{ВРПТ}', date('H:i', $this->time_getting_into_car), $list[1]);
            $list[2] = str_replace('{ВРПТ}', date('H:i', $this->time_getting_into_car), $list[2]);
        }
        if(!empty($this->point_id_from)) {
            $pointFrom = $this->pointFrom;
            $list[2] = str_replace('{ТЧК_ОТКУДА}', '&laquo;'.$pointFrom->name.'&raquo;', $list[2]);
        }

        return $list;
    }
    public function getRadioGroup2() {
        $list = [
            1 => 'Мы позвоним вам {ДАТА1} до 10:00 и скажем точное время и машину',
            2 => '{ДАТА2} вечером мы вам позвоним и скажем точное время и машину'
        ];

        if(!empty($this->date)) {
            if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
                $this->date = strtotime($this->date);
            }
            $list[1] = str_replace('{ДАТА1}', date('d.m.Y', $this->date), $list[1]);

            $date2 = $this->date - 86400;
            $list[2] = str_replace('{ДАТА2}', date('d.m.Y', $date2), $list[2]);
        }

        return $list;
    }
    public function getRadioGroup3() {
        return [
            1 => 'Когда поедете обратно? Давайте вас запишем.',
            2 => 'Спасибо за заказ. До свидания.'
        ];
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();


        // сценарий создания заказа при нажатии на кнопку "Записать"
        $scenarios['writedown_button_create'] = [
            'client_id',
            'status_id',
            'date',
            'tr_id',
            'direction_id',
            'point_id_from',
            'time_air_train_arrival',
            'point_id_to',
            'time_air_train_departure',
            'is_free',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'parent_id',
//            'time_getting_into_car',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_confirm',
            'categ_id',
            'time_sat',
            'use_fix_price',
            'price',
            'first_writedown_click_time',
            'first_writedown_clicker_id',
//            'first_confirm_click_time',
//            'first_confirm_clicker_id',
            'created_at',
            'updated_at',
//            'radio_group_1',
//            'radio_group_2',
//            'radio_group_3',
        ];

        // сценарий создания заказа при нажатии на кнопку "Подтвердить"
        $scenarios['confirm_button_create'] = [
            'client_id',
            'status_id',
            'date',
            'tr_id',
            'direction_id',
            'point_id_from',
            'time_air_train_arrival',
            'point_id_to',
            'time_air_train_departure',
            'is_free',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'parent_id',
            'time_getting_into_car',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_confirm',
            'categ_id',
            'time_sat',
            'use_fix_price',
            'price',
//            'first_writedown_click_time',
//            'first_writedown_clicker_id',
            'first_confirm_click_time',
            'first_confirm_clicker_id',
            'created_at',
            'updated_at',
//            'radio_group_1',
            'radio_group_2',
//            'radio_group_3',
        ];

        // сценарий обновления заказа при нажатии на кнопку "Записать"
        $scenarios['writedown_button_update'] = [
            'client_id',
            'status_id',
            //'date',
            'tr_id',
            'direction_id',
            'point_id_from',
            'time_air_train_arrival',
            'point_id_to',
            'time_air_train_departure',
            'is_free',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'parent_id',
//            'time_getting_into_car',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_confirm',
            'categ_id',
            'time_sat',
            'use_fix_price',
            'price',
            'first_writedown_click_time',
            'first_writedown_clicker_id',
//            'first_confirm_click_time',
//            'first_confirm_clicker_id',
            'created_at',
            'updated_at',
            'radio_group_1',
//            'radio_group_2',
//            'radio_group_3',
        ];

        // сценарий обновления заказа при нажатии на кнопку "Подтвердить"
        $scenarios['confirm_button_update'] = [
            'client_id',
            'status_id',
            //'date',
            'tr_id',
            'direction_id',
            'point_id_from',
            'time_air_train_arrival',
            'point_id_to',
            'time_air_train_departure',
            'is_free',
            'trip_id',
            'informer_office_id',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'parent_id',
            'time_getting_into_car',
            'comment',
            'additional_phone_1',
            'additional_phone_2',
            'additional_phone_3',
            'time_confirm',
            'categ_id',
            'time_sat',
            'use_fix_price',
            'price',
//            'first_writedown_click_time',
//            'first_writedown_clicker_id',
            'first_confirm_click_time',
            'first_confirm_clicker_id',
            'created_at',
            'updated_at',
            'radio_group_1',
            'radio_group_2',
            'radio_group_3',
        ];

        $scenarios['calculate_price'] = [
            //'client_id',
            //'point_id_from',
            'point_id_to',
            'is_not_places',
            'places_count',
            'student_count',
            'child_count',
            'bag_count',
            'suitcase_count',
            'oversized_count',
            'prize_trip_count',
            'use_fix_price',
            'price' // ?
        ];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_id' => 'Статус',
            'date' => 'Дата',
            'client_id' => 'Клиент',
            'tr_id' => 'tr_id',
            'direction_id' => 'Направление',
            'point_id_from' => 'Откуда',
            'point_id_to' => 'Куда',
            'time_air_train_arrival' => 'Время прибытия поезда / посадки самолета',
            'time_air_train_departure' => 'Время отправления поезда / начало регистрации авиарейса',
            'is_free' => 'Призовая поездка',
            'trip_id' => 'Рейс',
            'informer_office_id' => 'Информаторская',

            'is_not_places' => 'Без места', // отправляется посылка - т.е. занимается нефизическое место
            'places_count' => 'Количество мест всего',
            'student_count' => 'Количество студенческих мест',
            'child_count' => 'Количество детских всего',
            'bag_count' => 'Количество сумок',
            'suitcase_count' => 'Количество чемоданов',
            'oversized_count' => 'Количество негабаритов',
            'prize_trip_count' => 'Количество призовых поездок', // расчитывается в коде
            //'baggage' => 'Багаж',
            'parent_id' => 'Группа',
            'time_getting_into_car' => 'Время подтверждения',
            'comment' => 'Пожелания',
            'additional_phone_1' => 'Дополнительный телефон 1',
            'additional_phone_2' => 'Дополнительный телефон 2',
            'additional_phone_3' => 'Дополнительный телефон 3',
            'time_confirm' => 'Время подтверждения',
            'categ_id' => 'Категория',
            'time_sat' => 'Время посадки в машину',
            'price' => 'Цена',
            'first_writedown_click_time' => "Время первичного нажатия кнопки Записать",
            'first_writedown_clicker_id' => "Пользователь (диспетчер) впервые нажавший кнопку Записать",
            'first_confirm_click_time' => "Время первичного нажатия кнопки Подтвердить",
            'first_confirm_clicker_id' => "Пользователь (диспетчер) впервые нажавший кнопку Подтвердить",
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',

            'radio_group_1' => 'Группа переключателей для выбора времени готовности встречи с машиной',
            'radio_group_2' => 'Группа переключателей для выбора времени контрольного звонка',
            'radio_group_3' => 'Группа переключателей завершающих заказ'
        ];
    }


    public function beforeValidate()
    {
        if(!empty($this->places_count)) {
            $this->places_count = intval($this->places_count);
        }
        if(!empty($this->student_count)) {
            $this->student_count = intval($this->student_count);
        }
        if(!empty($this->child_count)) {
            $this->child_count = intval($this->child_count);
        }
        if(!empty($this->bag_count)) {
            $this->bag_count = intval($this->bag_count);
        }
        if(!empty($this->suitcase_count)) {
            $this->suitcase_count = intval($this->suitcase_count);
        }
        if(!empty($this->oversized_count)) {
            $this->oversized_count = intval($this->oversized_count);
        }

        if(isset($this->date) && preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $this->date)) {
            $this->date = strtotime($this->date);   // convent '07.11.2016' to unixtime
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
        }else {
            $this->updated_at = time();
        }


        if(isset($this->time_getting_into_car) && preg_match('/^[0-9]{2} : [0-9]{2}$/i', $this->time_getting_into_car)) {
            $hour_minute = explode(':', $this->time_getting_into_car);
            $this->time_getting_into_car = $this->date + trim($hour_minute[0])*3600 + trim($hour_minute[1])*60;
        }

        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/i', $this->time_confirm)) {
            $this->time_confirm = strtotime($this->time_confirm);   // convent '07.11.2016 01:25' to unixtime
        }

        if(preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}$/i', $this->time_sat)) {
            $this->time_sat = strtotime($this->time_sat);   // convent '07.11.2016 01:25' to unixtime
        }

        if(isset($this->use_fix_price) && $this->use_fix_price == 'on') {
            $this->use_fix_price = 1;
            $this->price = str_replace(' ', '', $this->price);
            // устаняю косяк обработки данных в js картика элементом MaskMoney
            if(strpos($this->price, '.') !== false) {
                $this->price = 1000 * $this->price;
            }

        }else {
            // рассчитаем цену на основе полученных данных

            $this->prize_trip_count = $this->prizeTripCount;
            $this->price = $this->calculatePrice;
        }

//        if(isset($this->fix_price)) {
//            $this->fix_price = str_replace(' ', '', $this->fix_price);
//            // устаняю косяк обработки данных в js картика элементом MaskMoney
//            if(strpos($this->fix_price, '.') !== false) {
//                $this->fix_price = 1000*$this->fix_price;
//            }
//        }


        // Обновление счетчиков
        if ($insert) {
            $client = $this->client;
            $client->order_count++;
            $client->prize_trip_count += $this->prize_trip_count;
            if(!$client->save(false)) {
                throw new ErrorException('Не удалось сохранить клиента');
            }
        }else {
            // если заказ пересохраняется то возможно:
            // - клиент привязанный к заказу отвалиться - у него нужно счетчик уменьшить на кол-во которое было добавлено ранее
            // - новому или текущему клиенту надо вначале вычесть количество которое было добавлено ранее в счетчик,
            // и потом добавить количество расчитанное по новым данным


            if($this->oldAttributes['client_id'] != $this->attributes['client_id']) {
                $old_client = Client::findOne($this->oldAttributes['client_id']);
                if($old_client == null) {
                    throw new ErrorException('Предыдущий клиент не найден');
                }

                $old_client->order_count--;
                $old_client->prize_trip_count -= $this->oldAttributes['prize_trip_count'];
                if(!$old_client->save(false)) {
                    throw new ErrorException('Не удалось обновить предыдущего клиента');
                }

                $new_client = Client::findOne($this->attributes['client_id']);
                if($new_client == null) {
                    throw new ErrorException('Новый клиент не найден');
                }

                $new_client->order_count++;
                $new_client->prize_trip_count += $this->attributes['prize_trip_count'];
                if(!$new_client->save(false)) {
                    throw new ErrorException('Не удалось обновить нового клиента');
                }
            }else // клиент не изменился, но могли измениться данные количества призовых поездок
            {
                //throw new ForbiddenHttpException('old_prize_trip_count='.$this->oldAttributes['prize_trip_count'].' new_prize_trip_count='.$this->prize_trip_count);
                if($this->oldAttributes['prize_trip_count'] != $this->attributes['prize_trip_count']) {
                    $client = $this->client;
                    $client->prize_trip_count = $client->prize_trip_count - $this->oldAttributes['prize_trip_count'] + $this->attributes['prize_trip_count'];
                    if(!$client->save(false)) {
                        throw new ErrorException('Не удалось сохранить клиента');
                    }
                }
            }
        }


        return parent::beforeSave($insert);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status_id']);
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
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointFrom()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_from']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPointTo()
    {
        return $this->hasOne(Point::className(), ['id' => 'point_id_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(Trip::className(), ['id' => 'trip_id']);
    }

    /*
     * Функция расчитывает и возвращает количество призовых поездок для текущего заказа
     */
    public function getPrizeTripCount()
    {
        // временные значения, позже добавлю расчет этих значений !!!
        $D = 0; // количество завершенных поездок (мест)
        $L = 0; // общее количество призовых поездок клиента

        $P = intval($this->places_count); // количество мест в текущем заказе

        if($this->is_not_places == 1)  // если отправляется посылка, то призовой поездки не предоставляется
            return 0;
        else
            return floor(($D - $L * self::LOYALITY + $P) / self::LOYALITY );
    }

    /*
     * Функция расчитывает и возвращает стоимость заказа
     */
    public function getCalculatePrice()
    {
        $P = intval($this->places_count); // количество мест в текущем заказе
        $S = intval($this->student_count); // количество студентов в текущем заказе
        $B = intval($this->child_count); // количество детей в текущем заказе

        $prize_count = $this->prizeTripCount; // количество призовых поездок в текущем заказе


        $arTariffs = ArrayHelper::map(Tariff::find()->all(), 'alias', 'cost');

        $T_COMMON = $arTariffs['T_COMMON'];  // цена по общему тарифу
        $T_STUDENT = $arTariffs['T_STUDENT']; // студенческий тариф
        $T_BABY = $arTariffs['T_BABY'];    // детский тариф
        $T_AERO = $arTariffs['T_AERO'];    // тариф аэропорт
        $T_LOYAL = $arTariffs['T_LOYAL'];   // тариф призовой поездки
        $T_PARCEL = $arTariffs['T_PARCEL']; // тариф отправки посылки (без места)

        // если клиенту едут в аэропорт, то они считаются по иной формуле
        $pointTo = $this->pointTo;
        $pointFrom = $this->pointFrom;

        if($this->is_not_places == 1) {
            $COST = $T_PARCEL;
        }elseif(
            ($pointTo != null && $pointTo->alias == 'airport')
            || ($pointFrom != null && $pointFrom->alias == 'airport')
        ) { // едут в аэропорт или из аэропорта
            $COST = ($P - $prize_count)*$T_AERO + $prize_count*$T_LOYAL;
        }else {
            $COST = ($P - $prize_count - $S - $B)*$T_COMMON + $S*$T_STUDENT + $B*$T_BABY + $prize_count*$T_LOYAL;
        }


        return $COST;
    }
}
