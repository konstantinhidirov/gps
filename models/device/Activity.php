<?php

namespace app\models\device;

use yii\db\ActiveRecord;

class Activity extends ActiveRecord
{
    public static function tableName()
    {
        return 'activity';
    }
}
