<?php

namespace app\models\device\track;

use yii\db\ActiveRecord;

class Pair extends ActiveRecord
{

    const MAX_DISTANCE = 6; // 6 meters
    const MIN_TIME = 600; // 10 min

    public static function tableName()
    {
        return 'tracks_pairs';
    }

}
