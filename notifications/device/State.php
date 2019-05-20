<?php

namespace app\notifications\device;

use app\lib\notification\transport\Comet;
use app\models\Device;
use app\models\device\Status;

class State
{
    /** @var Device $device */
    public $device;
    /** @var Status $status */
    public $status;

    public function channels()
    {
        return [
            Comet::class => [
                'event' => 'device/state',
                'channel' => $this->device->getNotificationChannel(),
                'data' => [
                    'status' => $this->status->state
                ]
            ]
        ];
    }
}