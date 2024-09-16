<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
        * This is the model class for table "user".
        *
        * @property integer $id
        * @property string $login
        * @property string $name
        * @property string $password
    */

    public function rules()
    {
        return [
            [['login', 'name', 'password'], 'required'],
            [['login', 'name', 'password'], 'string', 'max' => 255],
            [['login'], 'unique'],
        ];
    }


    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }


    public function getAuthKey()
    {
        return null;
    }
    
    public function validateAuthKey($authKey)
    {
        return false;
    }


   
}
