<?php
namespace app\commands;

use yii\console\Controller;
use app\models\User;
use Yii;

class UserController extends Controller
{
    public function actionCreate($name, $login, $password)
    {
        $user = new User();
        $user->name = $name;
        $user->login = $login;
        $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    
        if ($user->save()) {
            echo "User created successfully" . PHP_EOL;
            return;
        } 

        $errors = [];
        foreach ($user->getErrors() as $errorMessages) {
            $errors = array_merge($errors, $errorMessages);
        }

        echo "Error creating user: " . implode(PHP_EOL, $errors) . PHP_EOL;
    }
}
    
