<?php

use yii\db\Migration;

/**
 * Class m240916_041727_create_base_tables
 */
class m240916_042000_create_base_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'author' => $this->string(255)->notNull(),
            'isbn' => $this->string(13)->notNull()->unique(),
            'price' => $this->decimal(10, 2)->notNull(),
            'stock' => $this->integer()->notNull(),
        ]);

        $this->createTable('customer', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'cpf' => $this->string(11)->notNull()->unique(),
            'cep' => $this->string(8)->notNull(),
            'address' => $this->string(255)->notNull(),
            'number' => $this->string(10)->notNull(),
            'city' => $this->string(100)->notNull(),
            'state' => $this->string(2)->notNull(), 
            'complement' => $this->string(255)->null(),
            'sex' => $this->integer()->notNull(),
        ]);

        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'login' => $this->string(255)->notNull()->unique(),
            'name' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
        $this->dropTable('customer');
        $this->dropTable('book');
    }

}