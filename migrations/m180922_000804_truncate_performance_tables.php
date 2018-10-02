<?php

use console\components\Migration;

/**
 * Class m180922_000804_truncate_performance_tables
 */
class m180922_000804_truncate_performance_tables extends Migration
{
    const PERFORMANCE_LANGUAGE_SETTINGS_TABLE = 'performance_language_settings';
    const PERFORMANCE_ADMIN_SETTINGS_TABLE = 'performance_admin_settings';
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';
    const PERFORMANCE_TABLE = 'performance';

    public function safeUp()
    {
        $this->execute('set foreign_key_checks=0');
        $this->truncateTable(static::PERFORMANCE_ADMIN_SETTINGS_TABLE);
        $this->truncateTable(static::PERFORMANCE_COLUMN_SETTINGS_TABLE);
        $this->truncateTable(static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE);
        $this->truncateTable(static::PERFORMANCE_TABLE);
        $this->execute('set foreign_key_checks=1');
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180922_000804_truncate_performance_tables cannot be reverted.\n";
        return false;
    }
}
