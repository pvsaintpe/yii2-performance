<?php

namespace pvsaintpe\performance\models\query\base;

/**
 * This is the ActiveQuery class for [[\pvsaintpe\performance\models\PerformanceColumnSettings]].
 *
 * @see \pvsaintpe\performance\models\PerformanceColumnSettings
 */
class PerformanceColumnSettingsQueryBase extends \pvsaintpe\search\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\PerformanceColumnSettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\PerformanceColumnSettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer|integer[] $performanceId
     * @param string|string[] $attribute
     * @return $this
     */
    public function pk($performanceId, $attribute)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'attribute' => $attribute
        ]));
    }

    /**
     * @param integer|integer[] $performanceId
     * @param string|string[] $attribute
     * @return $this
     */
    public function performanceIdAttribute($performanceId, $attribute)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'attribute' => $attribute
        ]));
    }

    /**
     * @param integer|integer[] $performanceId
     * @return $this
     */
    public function performanceId($performanceId)
    {
        return $this->andWhere([$this->a('performance_id') => $performanceId]);
    }

    /**
     * @param string|string[] $attribute
     * @return $this
     */
    public function attribute($attribute)
    {
        return $this->andWhere([$this->a('attribute') => $attribute]);
    }

    /**
     * @param int|bool $ownAttribute
     * @return $this
     */
    public function ownAttribute($ownAttribute = true)
    {
        return $this->andWhere([$this->a('own_attribute') => $ownAttribute ? 1 : 0]);
    }

    /**
     * @param int|bool $valuesHidden
     * @return $this
     */
    public function valuesHidden($valuesHidden = true)
    {
        return $this->andWhere([$this->a('values_hidden') => $valuesHidden ? 1 : 0]);
    }

    /**
     * @param int|bool $required
     * @return $this
     */
    public function required($required = true)
    {
        return $this->andWhere([$this->a('required') => $required ? 1 : 0]);
    }

    /**
     * @param int|bool $protected
     * @return $this
     */
    public function protected($protected = true)
    {
        return $this->andWhere([$this->a('protected') => $protected ? 1 : 0]);
    }

    /**
     * @param int|bool $hidden
     * @return $this
     */
    public function hidden($hidden = true)
    {
        return $this->andWhere([$this->a('hidden') => $hidden ? 1 : 0]);
    }

    /**
     * @param int|bool $exportEnabled
     * @return $this
     */
    public function exportEnabled($exportEnabled = true)
    {
        return $this->andWhere([$this->a('export_enabled') => $exportEnabled ? 1 : 0]);
    }
}
