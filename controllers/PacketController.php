<?php

namespace app\controllers;

use app\lib\rest\RestController;

class PacketController extends RestController
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
            return false;
        if (array_key_exists('status', $this->request))
            $this->getDevice()->setStatus(
                $this->request['ts'],
                $this->request['status']
            );
        return true;
    }

    public function actionTrack()
    {
        return $this->getDevice()->setLocation(
            $this->request['ts'],
            $this->request['lat'],
            $this->request['lon'],
            $this->request['alt'],
            $this->request['accu']
        );
    }

    public function actionActivity()
    {
        return $this->getDevice()->setActivity(
            $this->request['ts'],
            $this->request['type'],
            $this->request['steps']
        );
    }
}