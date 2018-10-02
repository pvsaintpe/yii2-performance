<?php

use console\components\Migration;

/**
 * Class m180930_232739_fix_performance_migration
 */
class m180930_232739_fix_performance_migration extends Migration
{
    const PERFORMANCE_TABLE = 'performance';
    const LANGUAGE_TABLE = 'language';
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';
    const PERFORMANCE_ADMIN_SETTINGS_TABLE = 'performance_admin_settings';
    const PERFORMANCE_LANGUAGE_SETTINGS_TABLE = 'performance_language_settings';

    public function safeUp()
    {
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
        
    INSERT INTO `" . static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE . "` (`performance_id`, `language_id`, `name`)
    SELECT @performance_id, l.id, NEW.name
    FROM `" . static::LANGUAGE_TABLE . "` l;
    
    IF (NEW.`instance_performance_id` IS NOT NULL) THEN
        INSERT INTO `" . static::PERFORMANCE_COLUMN_SETTINGS_TABLE . "` (
            `performance_id`,
            `attribute`,
            `own_attribute`,
            `type`,
            `value`,
            `values_hidden`,
            `order_position`,
            `required`,
            `protected`,
            `hidden`,
            `sort_strategy`,
            `relation_class`,
            `relation_key`,
            `options`,
            `title`,
            `export_enabled`
        )
        SELECT 
            NEW.id,
            `pcs`.`attribute`,
            `pcs`.`own_attribute`,
            `pcs`.`type`,
            `pcs`.`value`,
            `pcs`.`values_hidden`,
            `pcs`.`order_position`,
            `pcs`.`required`,
            `pcs`.`protected`,
            `pcs`.`hidden`,
            `pcs`.`sort_strategy`,
            `pcs`.`relation_class`,
            `pcs`.`relation_key`,
            `pcs`.`options`,
            `pcs`.`title`,
            `pcs`.`export_enabled`
        FROM `" . static::PERFORMANCE_COLUMN_SETTINGS_TABLE . "` `pcs`
        WHERE `pcs`.`performance_id` = NEW.`instance_performance_id`;   
    END IF;
            ",
            'AFTER INSERT'
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180930_232739_fix_performance_migration cannot be reverted.\n";
        return false;
    }
}
