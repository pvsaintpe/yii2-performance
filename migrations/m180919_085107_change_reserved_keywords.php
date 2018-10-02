<?php

use console\components\Migration;

/**
 * Class m180919_085107_change_reserved_keywords
 */
class m180919_085107_change_reserved_keywords extends Migration
{
    const PERFORMANCE_TABLE = 'performance';
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';

    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->renameColumn(
            static::PERFORMANCE_TABLE,
            'order',
            'order_position'
        );

        $this->renameColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'order',
            'order_position'
        );

        $this->renameColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'sort',
            'sort_strategy'
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180919_085107_change_reserved_keywords cannot be reverted.\n";
        return false;
    }
}
