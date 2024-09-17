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
        * @property integer $number
        * @property string $city
        * @property string $state
        * @property string $complement
        * @property int $sex
        * @property string $s3_image_path
    */

    const MALE_SEX = 1;
    const FEMALE_SEX = 2;

    public function rules()
    {
        return [
            [['name', 'cpf', 'cep', 'address', 'city', 'state'], 'required'], 
            [['name', 'address', 'city', 'state', 'complement'], 'string', 'max' => 255],
            [['sex', 'number'], 'integer'],
            ['cpf', 'match', 'pattern' => '/^\d{11}$/', 'message' => 'CPF deve conter exatamente 11 dígitos.'], 
            ['cep', 'match', 'pattern' => '/^\d{8}$/', 'message' => 'CEP deve conter exatamente 8 dígitos.'],  
            ['cpf', 'validateCPF'],
            ['cep', 'validateCEP'],
            ['sex', 'in', 'range' => [self::MALE_SEX, self::FEMALE_SEX], 'message' => 'Sexo inválido.'],
            ['cpf', 'unique', 'message' => 'CPF já cadastrado.'],
            [['s3_image_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * Validates the CPF attribute by sanitizing it and checking if it is valid.
     * @param string $attribute
     * @param array $params
     * @return void
     */
    public function validateCPF($attribute, $params)
    {
        $cpf = $this->$attribute;
        $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }
    
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $this->addError($attribute, 'CPF inválido.');
                return;
            }
        }
    }

    /**
    * Validates the CEP attribute by sanitizing it and making a request to an external API in order to check if it is valid.
    * @param string $attribute
    * @param array $params
    * @return void
    */
    public function validateCEP($attribute, $params)
    {
        $cep = $this->$attribute;
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://brasilapi.com.br/api/cep/v1/$cep",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
    
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        if ($response === false || $httpCode != 200) {
            $this->addError($attribute, 'CEP inválido.');
            return;
        }
    }
    
   
}
