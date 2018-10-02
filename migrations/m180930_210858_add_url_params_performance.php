<?php

use console\components\Migration;

class m180930_210858_add_url_params_performance extends Migration
{
    const PERFORMANCE_TABLE = 'performance';

    public function safeUp()
    {
        $this->addColumn(
            static::PERFORMANCE_TABLE,
            'url_params',
            $this->string(255)->null()->comment('Параметры URL')->after('route')
        );

        $this->update(
            static::PERFORMANCE_TABLE,
            ['url_params' => 'N;']
        );
    }

    public function safeDown()
    {
        echo "m180930_210858_add_url_params_performance cannot be reverted.\n";
        return false;
    }
}
