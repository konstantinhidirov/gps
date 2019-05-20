<?php

namespace app\models\device;

use yii\db\ActiveRecord;

class Status extends ActiveRecord
{
    const STATUS_NON_WALK = 1;
    const STATUS_WALK = 2;

    public static function tableName()
    {
        return 'statuses';
    }
}
