<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CustomerSearch extends Customer
{
   
    public $cpf;
    public $name;
    public $city;
    
    const SORTABLE_FIELDS = ['cpf', 'name', 'city'];

    public function rules()
    {
        // Defina regras de validação para os atributos de pesquisa
        return [
            [self::SORTABLE_FIELDS, 'safe'],
        ];
    }

    public function search($params, $limit, $offset, $orderBy)
    {
        $query = Customer::find();
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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'cpf', $this->cpf]);

        return $dataProvider;
    }
}