<?php

use yii\db\Migration;

class m190515_214620_pairs_activity extends Migration
{
    public function safeUp()
    {
        $this->createTable('tracks_pairs', [
            'id1' => $this->integer(11)->notNull(),
            'id2' => $this->integer(11)->notNull()
        ]);
        $this->addPrimaryKey('pair', 'tracks_pairs', ['id1', 'id2']);

        $this->createTable('activity', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'device_id' => $this->integer(11)->notNull(),
            'track_id' => $this->integer(11)->defaultValue(null),
            'type' => $this->integer(11)->notNull(),
            'steps' => $this->integer(11)->notNull(),
            'ts' => $this->integer(11)->notNull()
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('tracks_pairs');
        $this->dropTable('activity');
    }
}
