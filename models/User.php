<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use app\models\UserRole;
//use yii\behaviors\TimestampBehavior;
//use yii\web\IdentityInterface;
//use yii\helpers\ArrayHelper;


class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public static function tableName()
    {
        return '{{%user}}';
    }


    public function rules()
    {
        return [
            [['username', 'firstname', 'lastname', 'role_id'], 'required'],
            [['username', 'email'], 'unique'],
            [['last_login_date', 'role_id', 'attempt_count', 'attempt_date', 'created_at', 'updated_at', 'blocked'], 'integer'],
            //[['last_ip', 'attempt_count', 'attempt_date'], 'required'],
            [['username', 'firstname', 'lastname', 'email', 'city'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'address'], 'string', 'max' => 255],
            [['phone', 'last_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_login_date' => 'Время последней попытки входа на сайт',
            'username' => 'Логин',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'email' => 'Электронная почта',
            'city' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'role_id' => 'Роль',
            'last_ip' => 'IP адрес (последнего входа на сайт)',
            'attempt_count' => 'Количество неудачных попыток последнего входа на сайт',
            'attempt_date' => 'Время последней попытки входа на сайт',
            'created_at' => 'Время создания',
            'updated_at' => 'Время изменения',
            'blocked' => 'Заблокирован',
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
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
    }

    public function getFullname() {
        return $this->lastname . ' ' . $this->firstname;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRole()
    {
        return $this->hasOne(UserRole::className(), ['id' => 'role_id']);
    }
}
