<?php

namespace app\models\device;

use yii\db\ActiveRecord;

class Cache extends ActiveRecord
{
    public static function tableName()
    {
        return 'devices_cache';
    }

    public static function getInPolygon(array $poly)
    {
        $polyData = [];
        foreach ($poly as $point)
            $polyData[] = implode(' ', $point);
        return self::find()->select(['device_id'])->where([
            'and',
            ['status_type' => Status::STATUS_WALK],
            'ST_Intersects(position, ST_GeomFromText(\'Polygon((' . implode(', ', $polyData) . '))\', 4326))'
        ])->column();
    }
}
