<?php

namespace common\models\query\base;

/**
 * This is the ActiveQuery class for [[\common\models\PerformanceLanguageSettings]].
 *
 * @see \common\models\PerformanceLanguageSettings
 */
class PerformanceLanguageSettingsQueryBase extends \common\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\PerformanceLanguageSettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\PerformanceLanguageSettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer|integer[] $performanceId
     * @param integer|integer[] $languageId
     * @return $this
     */
    public function pk($performanceId, $languageId)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'language_id' => $languageId
        ]));
    }

    /**
     * @param integer|integer[] $performanceId
     * @param integer|integer[] $languageId
     * @return $this
     */
    public function performanceIdLanguageId($performanceId, $languageId)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'language_id' => $languageId
        ]));
    }

    /**
     * @param integer|integer[] $languageId
     * @return $this
     */
    public function languageId($languageId)
    {
        return $this->andWhere([$this->a('language_id') => $languageId]);
    }

    /**
     * @param integer|integer[] $performanceId
     * @return $this
     */
    public function performanceId($performanceId)
    {
        return $this->andWhere([$this->a('performance_id') => $performanceId]);
    }
}
