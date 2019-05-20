<?php

use yii\db\Expression;
use yii\db\Migration;

class m190515_214619_device_data extends Migration
{
    public function safeUp()
    {
        $this->createTable('devices_cache', [
            'device_id' => $this->integer(11)->notNull()->append('PRIMARY KEY'),
            'position' => 'POINT NOT NULL SRID 4326',
            'location_lat' => $this->double(10)->defaultValue(null),
            'location_lon' => $this->double(10)->defaultValue(null),
            'location_alt' => $this->float(5)->defaultValue(null),
            'location_accu' => $this->integer(11)->defaultValue(null),
            'location_ts' => $this->integer(11)->notNull()->defaultValue(0),
            'status_type' => $this->tinyInteger(1)->defaultValue(null),
            'status_ts' => $this->integer(11)->notNull()->defaultValue(0),
            'track_id' => $this->integer(11)->defaultValue(null)
        ]);

        $this->execute('CREATE SPATIAL INDEX position ON devices_cache (position)');

        $zeroCoord = new Expression('ST_GeomFromText(\'POINT(0 0)\', 4326)');
        $this->batchInsert('devices_cache', ['device_id', 'position'], [[1, $zeroCoord], [2, $zeroCoord], [3, $zeroCoord]]);

        $this->createTable('tracks', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'device_id' => $this->integer(11)->notNull(),
            'begin_at' => $this->integer(11)->notNull(),
            'end_at' => $this->integer(11)->defaultValue(null)
        ]);

        $this->createTable('locations', [
            'device_id' => $this->integer(11)->notNull(),
            'track_id' => $this->integer(11)->defaultValue(null),
            'lat' => $this->double(10)->notNull(),
            'lon' => $this->double(10)->notNull(),
            'alt' => $this->float(5)->notNull(),
            'ts' => $this->integer(11)->notNull()
        ]);
        $this->addPrimaryKey('location_ts', 'locations', ['device_id', 'ts']);
    }

    public function safeDown()
    {
        $this->dropTable('devices_cache');
        $this->dropTable('locations');
        $this->dropTable('tracks');
    }
}
