<?php

namespace app\components;

use Yii;
use yii\filters\auth\AuthMethod;
use yii\web\UnauthorizedHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\IdentityInterface;
use app\models\User;

class TokenAuthenticator extends AuthMethod
{
    /**
     * Authenticates the user based on the token.
     * @param Request $request
     * @param Response $response
     * @return IdentityInterface|null 
     */
    public function authenticate($user, $request, $response)
    {
        $token = $request->getHeaders()->get('Authorization');

        if (!$token) {
            throw new UnauthorizedHttpException('Token de autenticação não informado.');
        }

        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        $user = User::findIdentityByAccessToken($token);
        if (!$user) {
            throw new UnauthorizedHttpException('Token de autenticação inválido.');
        }

        return $user;
    }
}
