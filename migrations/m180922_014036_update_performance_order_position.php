<?php

use console\components\Migration;

/**
 * Class m180922_014036_update_performance_order_position
 */
class m180922_014036_update_performance_order_position extends Migration
{
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';
    const PERFORMANCE_TABLE = 'performance';

    public function safeUp()
    {
        $this->alterColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'order_position',
            $this->smallInteger(5)->unsigned()->null()->defaultExpression('NULL')->comment('Порядок')
        );

        $this->alterColumn(
            static::PERFORMANCE_TABLE,
            'order_position',
            $this->smallInteger(5)->unsigned()->null()->defaultExpression('NULL')->comment('Порядок')
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180922_014036_update_performance_order_position cannot be reverted.\n";
        return false;
    }
}
