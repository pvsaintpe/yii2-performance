<?php

namespace pvsaintpe\performance\models;

use pvsaintpe\performance\models\base\PerformanceAdminSettingsBase;
use Yii;

/**
 * Performance admin settings
 * @see \pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery
 */
class PerformanceAdminSettings extends PerformanceAdminSettingsBase
{
    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedAdmin()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->performance_id)
            ->adminEnabled()
            ->one()) {
            return false;
        }

        return $model && $this->merchant_id != Yii::$app->user->id;
    }

    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedDelete()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->performance_id)
            ->deleteEnabled()
            ->one()) {
            return false;
        }

        return $model && $this->merchant_id != Yii::$app->user->id;
    }

    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedShare()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->performance_id)
            ->shareEnabled()
            ->one()) {
            return false;
        }

        return $model && $this->merchant_id != Yii::$app->user->id;
    }
}
