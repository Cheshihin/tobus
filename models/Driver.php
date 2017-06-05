<?php

namespace app\models;

use Yii;
use app\models\Transport;

/**
 * This is the model class for table "driver".
 *
 * @property integer $id
 * @property string $fio
 * @property string $mobile_phone
 * @property string $home_phone
 * @property integer $primary_transport_id
 * @property integer $secondary_transport_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Driver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fio', 'primary_transport_id'], 'required'],
            [['primary_transport_id', 'secondary_transport_id', 'created_at', 'updated_at'], 'integer'],
            [['fio'], 'string', 'max' => 100],
            [['mobile_phone'], 'string', 'max' => 15],
            [['home_phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
            'mobile_phone' => 'Мобильный телефон',
            'home_phone' => 'Домашний телефон',
            'primary_transport_id' => 'Основное транспортное средство',
            'secondary_transport_id' => 'Дополнительное транспортное средство',
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
    public function getPrimaryTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'primary_transport_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaryTransport()
    {
        return $this->hasOne(Transport::className(), ['id' => 'secondary_transport_id']);
    }
}
