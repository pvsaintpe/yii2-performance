<?php

use console\components\Migration;

/**
 * Class m180922_094321_update_performance_columns
 */
class m180922_094321_update_performance_columns extends Migration
{
    const PERFORMANCE_ADMIN_SETTINGS_TABLE = 'performance_admin_settings';
    const PERFORMANCE_LANGUAGE_SETTINGS_TABLE = 'performance_language_settings';
    const PERFORMANCE_TABLE = 'performance';
    const LANGUAGE_TABLE = 'language';

    public function safeUp()
    {
        $this->addColumn(
            static::PERFORMANCE_ADMIN_SETTINGS_TABLE,
            'order_position',
            $this->smallInteger(5)
                ->unsigned()
                ->null()
                ->comment('Порядок')
                ->after('enabled')
        );

        $this->createTrigger(
            static::PERFORMANCE_ADMIN_SETTINGS_TABLE,
            static::PERFORMANCE_ADMIN_SETTINGS_TABLE . '_BEFORE_INSERT',
            "
    SET @max_order_position = (
        SELECT MAX(`pas`.`order_position`) 
        FROM `" . static::PERFORMANCE_ADMIN_SETTINGS_TABLE . "` `pas`
        WHERE `pas`.`performance_id` = NEW.`performance_id`
        AND `pas`.`merchant_id` = NEW.`merchant_id`
    );
    
    IF (NEW.order_position IS NULL) THEN
        SET NEW.order_position = IFNULL(@max_order_position, 0) + 10;
    END IF;
            ",
            'BEFORE INSERT'
        );

        $this->createTrigger(
            static::PERFORMANCE_TABLE,
            static::PERFORMANCE_TABLE . '_AFTER_INSERT',
            "
    SET @performance_id = NEW.`id`;
    
    INSERT INTO `" . static::PERFORMANCE_ADMIN_SETTINGS_TABLE . "` 
    SET 
		`performance_id` = @performance_id, 
        `merchant_id` = NEW.`merchant_id`, 
        `admin_enabled` = 1, 
        `view_enabled` = 1, 
        `edit_enabled` = 1, 
        `share_enabled` = 1, 
        `switch_enabled` = 1, 
        `delete_enabled` = 1,
        `order_position` = NEW.`order_position`;
        
    INSERT INTO `"  . static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE . "` (`performance_id`, `language_id`, `name`)
    SELECT @performance_id, l.id, NEW.name
    FROM `"  . static::LANGUAGE_TABLE . "` l;
            ",
            'AFTER INSERT'
        );
    }

    public function safeDown()
    {
        echo "m180922_094321_update_performance_columns cannot be reverted.\n";
        return false;
    }
}
