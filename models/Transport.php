<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transport".
 *
 * @property integer $id
 * @property string $model
 * @property string $sh_model
 * @property string $car_reg
 * @property integer $places_count
 * @property string $color
 * @property integer $created_at
 * @property integer $updated_at
 */
class Transport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['places_count', 'created_at', 'updated_at'], 'integer'],
            [['model', 'color'], 'string', 'max' => 50],
            [['sh_model', 'car_reg'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model' => 'Марка',
            'sh_model' => 'Сокращенное название',
            'car_reg' => 'Гос. номер',
            'places_count' => 'Количество мест',
            'color' => 'Цвет',
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

    public function getName() {
        return $this->model.' (рег.номер '.$this->car_reg.')';
    }
}
