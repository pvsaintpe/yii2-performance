<?php

use console\components\Migration;

/**
 * Class m180921_234231_add_performance_columns
 */
class m180921_234231_add_performance_columns extends Migration
{
    const PERFORMANCE_LANGUAGE_SETTINGS_TABLE = 'performance_language_settings';
    const PERFORMANCE_ADMIN_SETTINGS_TABLE = 'performance_admin_settings';
    const PERFORMANCE_COLUMN_SETTINGS_TABLE = 'performance_column_settings';
    const PERFORMANCE_TABLE = 'performance';
    const LANGUAGE_TABLE = 'language';

    public function safeUp()
    {
        $this->addColumn(
            static::PERFORMANCE_ADMIN_SETTINGS_TABLE,
            'is_default',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('По умолчанию')
                ->after('merchant_id')
        );

        $this->addColumn(
            static::PERFORMANCE_ADMIN_SETTINGS_TABLE,
            'enabled',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(1)
                ->comment('Вкл.')
                ->after('is_default')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'type',
            $this->string(45)
                ->notNull()
                ->defaultValue('raw')
                ->comment('Тип')
                ->after('attribute')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'hidden',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('Скрытое')
                ->after('required')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'export_enabled',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('Разрешено для экспорта')
                ->after('options')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'protected',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('Защищенное')
                ->after('required')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'values_hidden',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(1)
                ->comment('Значения скрыты')
                ->after('value')
        );

        $this->addColumn(
            static::PERFORMANCE_COLUMN_SETTINGS_TABLE,
            'title',
            $this->string(255)
                ->null()
                ->defaultExpression('NULL')
                ->comment('Описание')
                ->after('options')
        );

        $this->addColumn(
            static::PERFORMANCE_TABLE,
            'system_defined',
            $this->tinyInteger(1)
                ->unsigned()
                ->notNull()
                ->defaultValue(0)
                ->comment('Системное')
                ->after('enabled')
        );

        $this->createTableWithComment(static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE, [
            'performance_id' => $this->integer(10)->unsigned()->notNull()->comment('Представление'),
            'language_id' => $this->tinyInteger(3)->unsigned()->notNull()->comment('Язык'),
            'name' => $this->string(255)->notNull()->comment('Название'),
            'description' => $this->text()->null()->comment('Описание'),
        ], 'Локализация представлений');

        $this->addPrimaryKey(
            null,
            static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE,
            [
                'performance_id',
                'language_id'
            ]
        );

        $this->addForeignKey(
            null,
            static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE,
            'performance_id',
            static::PERFORMANCE_TABLE,
            'id'
        );

        $this->addForeignKey(
            null,
            static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE,
            'language_id',
            static::LANGUAGE_TABLE,
            'id'
        );

        $this->createTrigger(
            static::LANGUAGE_TABLE,
            static::LANGUAGE_TABLE . '_AFTER_INSERT',
            "
    SET @language_id = NEW.id;
   
    INSERT INTO `store_language_settings` (`store_id`, `language_id`, `project_id`, `merchant_id`, `store_group_id`)
    SELECT s.id, @language_id, s.project_id, s.merchant_id, s.store_group_id
    FROM `store` s;
    
    INSERT INTO `store_mail_language_settings` (`store_id`, `language_id`, `project_id`, `merchant_id`, `store_group_id`)
    SELECT s.id, @language_id, s.project_id, s.merchant_id, s.store_group_id
    FROM `store` s;
    
    INSERT INTO `store_group_mail_language_settings` (`language_id`, `project_id`, `merchant_id`, `store_group_id`)
    SELECT @language_id, sg.project_id, sg.merchant_id, sg.id
    FROM `store_group` sg;
    
    INSERT INTO `project_mail_language_settings` (`language_id`, `project_id`, `merchant_id`)
    SELECT @language_id, p.id, p.merchant_id
    FROM `project` p;

    INSERT INTO `method_currency_language_settings` (`store_id`, `payment_method_id`, `payment_system_id`, `currency_id`, `language_id`, `project_id`, `merchant_id`, `store_group_id`)
    SELECT s.id, pm.id, ps.id, c.id, @language_id, s.project_id, s.merchant_id, s.store_group_id
    FROM `store` s, `payment_method` pm, `payment_system` ps, currency c;
    
    INSERT INTO `" . static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE . "` (`performance_id`, `language_id`, `name`)
    SELECT p.id, @language_id, ''
    FROM `" . static::PERFORMANCE_TABLE . "` p;
            ",
            'AFTER INSERT'
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
        `delete_enabled` = 1;
        
    INSERT INTO `" . static::PERFORMANCE_LANGUAGE_SETTINGS_TABLE . "` (`performance_id`, `language_id`, `name`)
    SELECT @performance_id, l.id, NEW.`name`
    FROM `" . static::LANGUAGE_TABLE . "` l;
            ",
            'AFTER INSERT'
        );
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        echo "m180921_234231_add_performance_columns cannot be reverted.\n";
        return false;
    }
}
