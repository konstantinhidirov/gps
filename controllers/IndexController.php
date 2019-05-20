<?php

namespace app\controllers;

use app\lib\rest\RestController;

class IndexController extends RestController
{
    public function behaviors()
    {
        $b = parent::behaviors();
        if ($this->action->id === 'index')
            unset($b['authenticator']);
        return $b;
    }

    public function actionIndex()
    {
        return 'ok';
    }

    public function actionTestAuth()
    {
        return $this->getDevice();
    }
}