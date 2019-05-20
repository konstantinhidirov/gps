<?php

use yii\db\Migration;

class m190515_214618_device_status extends Migration
{
    public function safeUp()
    {
        $this->createTable('statuses', [
            'device_id' => $this->integer(11)->notNull(),
            'type' => $this->smallInteger(1)->notNull(),
            'ts' => $this->integer(11)->notNull()
        ]);

        $this->addPrimaryKey('status_ts', 'statuses', ['device_id', 'ts']);
    }

    public function safeDown()
    {
        $this->dropTable('statuses');
    }
}
