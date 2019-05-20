<?php

namespace app\models\device;

use yii\db\ActiveRecord;

class Location extends ActiveRecord
{

    public static function tableName()
    {
        return 'locations';
    }
}
