<?php

namespace app\controllers;

use app\lib\rest\RestController;
use app\models\Device;

class DeviceController extends RestController
{
    public function actionGet()
    {
        $data = $this->getDevice()->cache->attributes;
        unset($data['position']);
        return $data;
    }

    public function actionBetween()
    {
        return Device::getInPolygon($this->request['polygon']);
        /*return Device::getInPolygon([
            [46.331876, 48.048969],
            [46.328750, 48.049064],
            [46.328638, 48.046401],
            [46.331309, 48.045747],
            [46.331876, 48.048969]
        ]);*/
    }
}