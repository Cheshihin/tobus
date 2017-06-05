<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tariff".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property string $cost
 * @property integer $date_from
 * @property integer $date_to
 * @property integer $created_at
 * @property integer $updated_at
 */
class Tariff extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'alias',], 'required'],
            [['cost'], 'number'],
            [[/*'active',*/ 'created_at', 'updated_at'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 100],
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
            'alias' => 'Псевдоним (на английском)',
            'cost' => 'Стоимость',
            //'active' => 'Активен',
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
}
