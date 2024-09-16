<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


class ApiController extends Controller
{
 
    public function behaviors()
    {
        return [
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['POST'],  
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['protected-route-1', 'protected-route-2'],
                'rules' => [
                    [
                        'actions' => ['protected-route-1', 'protected-route-2'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['login'],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

   
    public function actionLogin()
    {
        $request = Yii::$app->request;
        $login = $request->post('login');
        $password = $request->post('password');
        Yii::$app->response->statusCode = 400;


        if (!$login || !$password) {
            return [
                'success' => false,
                'message' => 'Login e senha são obrigatórios',
            ];
        }

        $user = User::findOne(['login' => $login]);
        if (!$user || !Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Login ou senha inválidos',
            ];
        }

        $secretKey = 'your-secret-key';  // Substitua por uma chave secreta
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; 
        $payload = [
            'iat' => $issuedAt,               // Issued at
            'exp' => $expirationTime,         // Expiration time
            'data' => [
                'userId' => $user->id,
                'login' => $user->login,
            ],
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return [
            'success' => true,
            'token' => $jwt,
        ];
    }
  
}