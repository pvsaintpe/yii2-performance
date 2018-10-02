<?php

use console\components\Migration;

/**
 * Class m180922_102309_drop_performance_default_column
 */
class m180922_102309_drop_performance_default_column extends Migration
{
    const PERFORMANCE_TABLE = 'performance';

    public function safeUp()
    {
        $this->dropColumn(static::PERFORMANCE_TABLE, 'is_default');
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180922_102309_drop_performance_default_column cannot be reverted.\n";
        return false;
    }
}
