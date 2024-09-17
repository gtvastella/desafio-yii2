<?php

namespace app\models;

use yii\db\ActiveRecord;
use Exception;

class Book extends ActiveRecord
{
    /**
        * @property integer $id
        * @property string $title
        * @property string $author
        * @property string $isbn
        * @property string $price
        * @property string $stock
        * @property string $s3_image_path
    */

    public function rules()
    {
        return [
            [['title', 'author', 'isbn', 'price', 'stock'], 'required'], 
            [['title', 'author', 'isbn'], 'string', 'max' => 255],
            [['price'], 'number'],
            [['stock'], 'integer'],
            [['stock'], 'compare', 'compareValue' => 0, 'operator' => '>=', 'message' => 'Estoque deve ser maior ou igual a zero.'],
            ['isbn', 'match', 'pattern' => '/^\d{10,13}$/', 'message' => 'ISBN deve conter 10 ou 13 dígitos.'],
            ['isbn', 'unique', 'message' => 'ISBN já cadastrado.'],
            ['isbn', 'validateISBN'],
            [['s3_image_path'], 'string', 'max' => 255],
        ];
    }

     /**
    * Validates the ISBN attribute by sanitizing it and checking if it is valid using the BrasilAPI.
    * @param string $attribute
    * @param array $params
    * @return void
    */
    public function validateISBN($attribute, $params)
    {
        $isbn = $this->$attribute;
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://brasilapi.com.br/api/isbn/v1/$isbn",
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
            $this->addError($attribute, 'ISBN inválido.');
            return;
        }
    }

    /**
     * Attempts to populate the book data using the BrasilAPI.
     * @param array $fields Which fields to populate.
     * @return bool
     */
    public function populateDataByISBN($fields)
    {
        $curl = curl_init();
        $isbn = $this->isbn;
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://brasilapi.com.br/api/isbn/v1/$isbn",
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
            return false;
        }
        try {
            $data = json_decode($response, true);
            foreach ($fields as $field) {
                $this->$field = ($field == "author") ? $data["authors"][0] : ($data[$field]);
            }
        } catch (Exception $e) {
            return false;
        }
    
        return true;
    }
   
}
