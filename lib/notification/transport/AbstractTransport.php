<?php

namespace app\lib\notification\transport;

use yii\base\BaseObject;

abstract class AbstractTransport extends BaseObject
{
    public abstract function send();
}