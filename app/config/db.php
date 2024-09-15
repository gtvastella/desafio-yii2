<?php

use Dotenv\Dotenv;

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv("DB_HOST") . ';dbname=' . getenv("MYSQL_DATABASE"),
    'username' => 'root',
    'password' => getenv("MYSQL_ROOT_PASSWORD"),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
