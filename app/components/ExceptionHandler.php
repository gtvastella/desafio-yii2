<?php

namespace app\components;

use Yii;
use yii\web\ErrorHandler;
use yii\web\UnauthorizedHttpException;
use yii\web\Response;

class ExceptionHandler extends ErrorHandler
{
    /**
     * Formats the exception into a response.
     * @param \Exception $exception the exception to be handled.
     */
    protected function renderException($exception)
    {
        Yii::$app->response->statusCode = ($exception->statusCode == 200 ? 500 : $exception->statusCode);
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = [
            "success" => false,
            'message' => $exception->getMessage()
        ];

        return Yii::$app->response->send();
    }
}
