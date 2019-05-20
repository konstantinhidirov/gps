<?php

namespace app\lib\notification;

use app\lib\notification\transport\AbstractTransport;
use yii\base\BaseObject;

abstract class Notification extends BaseObject
{

    /** @return AbstractTransport[] */
    public function channels()
    {
        return [];
    }

    public function send()
    {
        foreach (static::channels() as $chClass => $data) {
            $channel = new $chClass($data);
            /** @var AbstractTransport $channel */
            $channel->send();
        }
    }

    public static function create($data = [])
    {
        $n = new static($data);
        $n->send();
    }
}