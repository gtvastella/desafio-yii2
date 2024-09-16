<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class Customer extends ActiveRecord
{
    /**
        * @property integer $id
        * @property string $name
        * @property string $cpf
        * @property string $cep
        * @property string $address
        * @property string $number
        * @property string $city
        * @property string $state
        * @property string $complement
        * @property int $sex
    */

    public function rules()
    {
        return [
            [['name', 'cpf', 'cep', 'address', 'number', 'city', 'state'], 'required'], 
            [['name', 'address', 'city', 'state', 'complement'], 'string', 'max' => 255],
            ['cpf', 'string', 'length' => [11]],  
            ['cep', 'string', 'length' => [8]],   
            ['number', 'string', 'max' => 10],    
            ['sex', 'integer'],                  
            ['cpf', 'match', 'pattern' => '/^\d{11}$/', 'message' => 'CPF deve conter exatamente 11 dígitos.'], 
            ['cep', 'match', 'pattern' => '/^\d{8}$/', 'message' => 'CEP deve conter exatamente 8 dígitos.'],  
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

   
}
