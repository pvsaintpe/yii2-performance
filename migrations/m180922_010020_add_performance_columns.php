<?php

use console\components\Migration;

/**
 * Class m180922_010020_add_performance_columns
 */
class m180922_010020_add_performance_columns extends Migration
{
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';

    public function safeUp()
    {
        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'own_attribute',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('Родной аттрибут')
                ->after('attribute')
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180922_010020_add_performance_columns cannot be reverted.\n";
        return false;
    }
}
