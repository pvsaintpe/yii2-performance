<?php

namespace common\models;

use common\models\base\PerformanceBase;
use common\models\query\PerformanceAdminSettingsQuery;
use common\models\query\PerformanceQuery;
use Yii;
use yii\db\Expression;

/**
 * Performance
 * @see \common\models\query\PerformanceQuery
 */
class Performance extends PerformanceBase
{
    /**
     * @param array $filters
     * @param bool $globalFilters
     * @param bool $withNotSet
     * @return array
     */
    public static function findForFilter($filters = [], $globalFilters = true, $withNotSet = false)
    {
        $performanceQuery = static::findFilterQuery($filters)
            ->innerJoinWith([
                'performanceAdminSettings pas' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                    $performanceAdminSettingsQuery->viewEnabled(1);
                    $performanceAdminSettingsQuery->enabled();
                    $performanceAdminSettingsQuery->andWhere([
                        'OR',
                        ['expired_at' => null],
                        ['>=', 'expired_at', new Expression('NOW()')],
                    ]);
                }
            ])
            ->orderBy(['f.order_position' => SORT_ASC]);

        $defaultQuery = clone $performanceQuery;
        $records = $performanceQuery->column();
        $result = [];
        if ($defaultQuery->andWhere(['pas.is_default' => 1])->count() == 0) {
            $result = [
                0 => Yii::t('layout', 'По умолчанию'),
            ];
        }
        foreach ($records as $key => $name) {
            if ($template = PerformanceLanguageSettings::find()
                ->performanceId($key)
                ->languageId(Language::getIdByCode(Yii::$app->language))
                ->one()
            ) {
                $result[$key] = $template->name;
            } else {
                $result[$key] = $name;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function titleKey()
    {
        return [
            'name'
        ];
    }

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
            ->performanceId($this->id)
            ->adminEnabled()
            ->one()) {
            return false;
        }

        return $model;
    }

    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedEdit()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->id)
            ->editEnabled()
            ->one()) {
            return false;
        }

        return $model;
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
            ->performanceId($this->id)
            ->deleteEnabled()
            ->one()) {
            return false;
        }

        return $model;
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
            ->performanceId($this->id)
            ->shareEnabled()
            ->one()) {
            return false;
        }

        return $model;
    }

    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedSwitch()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->id)
            ->switchEnabled()
            ->one()) {
            return false;
        }

        return $model;
    }

    /**
     * @return array|bool|PerformanceAdminSettings|null
     */
    public function allowedView()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = PerformanceAdminSettings::find()
            ->merchantId(Yii::$app->user->id)
            ->performanceId($this->id)
            ->viewEnabled()
            ->one()) {
            return false;
        }

        return $model;
    }

    /**
     * @return bool|int|mixed
     */
    public function isDefault()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = $this->getAdminSettings()) {
            return false;
        }

        return $model->is_default;
    }

    /**
     * @return bool|int|mixed
     */
    public function isEnabled()
    {
        if (Yii::$app->id != 'app-backend') {
            return false;
        }

        if (!$model = $this->getAdminSettings()) {
            return false;
        }

        return $model->enabled;
    }

    /**
     * @return array|PerformanceAdminSettings|null|\yii\db\ActiveRecord
     */
    public function getAdminSettings()
    {
        return $this->getPerformanceAdminSettings()
            ->merchantId(Yii::$app->getUser()->getId())
            ->one();
    }
}
