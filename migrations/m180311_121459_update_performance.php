<?php

use console\components\Migration;

class m180311_121459_update_performance extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            'performance_column_settings',
            'value',
            $this->string()->null()->defaultValue(new \yii\db\Expression('NULL'))->comment('Значение по умолчанию')
        );

        $this->addColumn(
            'performance_column_settings',
            'relation_class',
            $this->string()->null()->defaultValue(new \yii\db\Expression('NULL'))->comment('Класс')
        );

        $this->addColumn(
            'performance_column_settings',
            'relation_key',
            $this->string()->null()->defaultValue(new \yii\db\Expression('NULL'))->comment('Связь')
        );

        $this->addColumn(
            'performance_column_settings',
            'options',
            $this->text()->null()->defaultValue(new \yii\db\Expression('NULL'))->comment('Опции')
        );
    }

    public function safeDown()
    {
        echo "m180311_121459_update_performance cannot be reverted.\n";
        return false;
    }
}
