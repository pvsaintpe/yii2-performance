<?php

use console\components\Migration;

class m180311_131818_update_performance_table extends Migration
{
    public function safeUp()
    {
        $this->createUnique(
            null,
            'performance',
            [
                'route',
                'search_class',
                'merchant_id',
                'name',
            ]
        );
    }

    public function safeDown()
    {
        echo "m180311_131818_update_performance_table cannot be reverted.\n";
        return false;
    }
}
