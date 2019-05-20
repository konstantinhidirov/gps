<?php

use yii\db\Migration;

class m190515_203836_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('devices', [
            'id' => $this->integer(11)->notNull()->append('AUTO_INCREMENT PRIMARY KEY'),
            'token' => $this->string(100),
        ]);

        $this->createIndex('token', 'devices', 'token', true);

        $this->batchInsert('devices', ['id', 'token'], [[1, 'device1'], [2, 'device2'], [3, 'device3']]);
    }

    public function safeDown()
    {
        $this->dropTable('devices');
    }
}