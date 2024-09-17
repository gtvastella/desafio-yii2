<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BookSearch extends Book
{
   
    public $title;
    public $price;
    public $author;
    public $isbn;
    
    const SORTABLE_FIELDS = ['title', 'price'];

    public function rules()
    {
        return [
            [['title', 'price', 'author', 'isbn'], 'safe'],
        ];
    }

    public function search($params, $limit, $offset, $orderBy)
    {
        $query = Book::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
                'page' => $offset,
            ],
            'sort' => [
                'defaultOrder' => [
                    $orderBy => SORT_ASC,
                ],
                'attributes' => self::SORTABLE_FIELDS,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'isbn', $this->isbn]);

        return $dataProvider;
    }
}