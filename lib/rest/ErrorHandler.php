<?php

namespace app\lib\rest;

class ErrorHandler extends \yii\web\ErrorHandler
{
    protected function convertExceptionToArray($exception)
    {
        $array = parent::convertExceptionToArray($exception);
        $array['message'] = $exception->getMessage();
        return $array;
    }
}
