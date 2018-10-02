<?php

use console\components\Migration;

/**
 * Class m180921_225408_create_performance_triggers
 */
class m180921_225408_create_performance_triggers extends Migration
{
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';
    const PERFORMANCE_TABLE = 'performance';

    public function safeUp()
    {
        $this->createTrigger(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE . '_BEFORE_INSERT',
            "
    SET @max_order_position = (
        SELECT MAX(`pcs`.`order_position`) 
        FROM `" . static::PERFORMANCE_COLUMN_SETTINGS_TABLE . "` `pcs`
        WHERE `pcs`.`performance_id` = NEW.`performance_id`
    );
    
    IF (NEW.order_position IS NULL) THEN
        SET NEW.order_position = IFNULL(@max_order_position, 0) + 10;
    END IF;
            ",
            'BEFORE INSERT'
        );

        $this->createTrigger(
            static::PERFORMANCE_TABLE,
            static::PERFORMANCE_TABLE . '_BEFORE_INSERT',
            "
    SET @max_order_position = (
        SELECT MAX(`p`.`order_position`) 
        FROM `" . static::PERFORMANCE_TABLE . "` `p`
        WHERE `p`.`merchant_id` = NEW.`merchant_id`
    );
    
    IF (NEW.order_position IS NULL) THEN
        SET NEW.order_position = IFNULL(@max_order_position, 0) + 10;
    END IF;
            ",
            'BEFORE INSERT'
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180921_225408_create_performance_triggers cannot be reverted.\n";
        return false;
    }
}
