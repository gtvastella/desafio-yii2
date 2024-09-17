<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;


class User extends ActiveRecord implements IdentityInterface
{
    /**
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


    public static function findIdentity($id): mixed
    {
        return self::findOne($id);
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null): mixed
    {
        try {
            $secret = getenv(name: 'JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $userId = $decoded->userId;
            $user = self::findOne($userId);
            return $user;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAuthKey(): null
    {
        return null;
    }
    
    public function validateAuthKey($authKey): bool
    {
        return false;
    }

    public function createBearerToken(): mixed
    {
        try {
            $secret = getenv(name: 'JWT_SECRET');
            $token = [
                'iat' => time(),
                'exp' => time() + 3600,
                'userId' => $this->id,
            ];
    
            return JWT::encode($token, $secret, 'HS256');
        } catch (Exception $e) {
            return null;
        }
    }

    public function validateToken($token): mixed
    {
        try {
            $secret = getenv(name: 'JWT_SECRET');
            return JWT::decode($token, $secret);
        } catch (Exception $e) {
            return false;
        }
    }
    
   
}
