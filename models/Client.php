<?php

namespace app\models;

use Yii;
use app\models\Point;

/**
 * This is the model class for table "client".
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile_phone
 * @property string $home_phone
 * @property string $alt_phone
 * @property integer $rating
 * @property integer $prize_trip_count
 * @property integer $created_at
 * @property integer $updated_at
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile_phone'], 'required'],
            [[/*'last_point_from', 'last_point_to',*/ 'order_count', 'rating', 'prize_trip_count', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['mobile_phone'], 'string', 'max' => 15],
            [['home_phone', 'alt_phone'], 'string', 'max' => 20],
            [['mobile_phone'], 'unique'],
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
            'mobile_phone' => 'Мобильный телефон',
            'home_phone' => 'Домашний телефон',
            'alt_phone' => 'Дополнительный телефон',
//            'last_point_from' => 'Последняя точка отправки',
//            'last_point_to' => 'Последняя точка прибытия',
            'order_count' => 'Количество заказов',
            'rating' => 'Рейтинг',
            'prize_trip_count' => 'Количество призовых поездок',
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
//    public function getLastPointFrom()
//    {
//        return $this->hasOne(Point::className(), ['id' => 'last_point_from']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getLastPointTo()
//    {
//        return $this->hasOne(Point::className(), ['id' => 'last_point_to']);
//    }


    /*
     * Поиск клиента по мобильному номеру телефона
     */
    public function getClientByMobilePhone($mobile_phone)
    {
        $mobile_phone = trim($mobile_phone);

        if(empty($mobile_phone)) {
            return null;
        }

        if($mobile_phone[0] != '+') {
            $mobile_phone = '+' . $mobile_phone;
        }

        $client = Client::find()->where(['mobile_phone' => $mobile_phone])->one();

        return $client;
    }
}
