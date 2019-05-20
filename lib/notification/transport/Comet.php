<?php

namespace app\lib\notification\transport;

class Comet extends AbstractTransport
{
    public $channel;
    public $event;
    public $data;

    public function send()
    {
        // поместить событие в очередь отправки (AMQP?)
    }
}