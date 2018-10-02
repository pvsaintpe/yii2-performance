<?php

use console\components\Migration;

/**
 * Class m180923_081722_update_performance_export_column
 */
class m180923_081722_update_performance_export_column extends Migration
{
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';

    public function safeUp()
    {
        $this->alterColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'export_enabled',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(1)
                ->comment('Разрешено для экспорта')
        );

        $this->update(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            ['export_enabled' => 1]
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180923_081722_update_performance_export_column cannot be reverted.\n";
        return false;
    }
}
