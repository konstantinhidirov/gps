<?php

namespace app\lib\rest;

class Response extends \yii\web\Response
{
    protected $message = 'success';

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
