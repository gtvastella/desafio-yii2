<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class Book extends ActiveRecord
{
    /**
        * @property integer $id
        * @property string $title
        * @property string $author
        * @property string $isbn
        * @property string $price
        * @property string $stock
    */

    public function rules()
    {
        return [
            [['title', 'author', 'isbn', 'price', 'stock'], 'required'], 
            [['title', 'author', 'isbn'], 'string', 'max' => 255],
            [['price', 'stock'], 'string', 'max' => 10],  
            ['isbn', 'match', 'pattern' => '/^\d{13}$/', 'message' => 'ISBN deve conter exatamente 13 dígitos.'], 
        ];
    }

   
}
