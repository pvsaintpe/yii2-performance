<?php

namespace pvsaintpe\performance\models\query\base;

/**
 * This is the ActiveQuery class for [[\pvsaintpe\performance\models\PerformanceAdminSettings]].
 *
 * @see \pvsaintpe\performance\models\PerformanceAdminSettings
 */
class PerformanceAdminSettingsQueryBase extends \pvsaintpe\search\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\PerformanceAdminSettings[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\PerformanceAdminSettings|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer|integer[] $performanceId
     * @param integer|integer[] $merchantId
     * @return $this
     */
    public function pk($performanceId, $merchantId)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'merchant_id' => $merchantId
        ]));
    }

    /**
     * @param integer|integer[] $performanceId
     * @param integer|integer[] $merchantId
     * @return $this
     */
    public function performanceIdMerchantId($performanceId, $merchantId)
    {
        return $this->andWhere($this->a([
            'performance_id' => $performanceId,
            'merchant_id' => $merchantId
        ]));
    }

    /**
     * @param integer|integer[] $merchantId
     * @return $this
     */
    public function merchantId($merchantId)
    {
        return $this->andWhere([$this->a('merchant_id') => $merchantId]);
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
     * @param int|bool $isDefault
     * @return $this
     */
    public function isDefault($isDefault = true)
    {
        return $this->andWhere([$this->a('is_default') => $isDefault ? 1 : 0]);
    }

    /**
     * @param int|bool $enabled
     * @return $this
     */
    public function enabled($enabled = true)
    {
        return $this->andWhere([$this->a('enabled') => $enabled ? 1 : 0]);
    }

    /**
     * @param int|bool $adminEnabled
     * @return $this
     */
    public function adminEnabled($adminEnabled = true)
    {
        return $this->andWhere([$this->a('admin_enabled') => $adminEnabled ? 1 : 0]);
    }

    /**
     * @param int|bool $viewEnabled
     * @return $this
     */
    public function viewEnabled($viewEnabled = true)
    {
        return $this->andWhere([$this->a('view_enabled') => $viewEnabled ? 1 : 0]);
    }

    /**
     * @param int|bool $editEnabled
     * @return $this
     */
    public function editEnabled($editEnabled = true)
    {
        return $this->andWhere([$this->a('edit_enabled') => $editEnabled ? 1 : 0]);
    }

    /**
     * @param int|bool $shareEnabled
     * @return $this
     */
    public function shareEnabled($shareEnabled = true)
    {
        return $this->andWhere([$this->a('share_enabled') => $shareEnabled ? 1 : 0]);
    }

    /**
     * @param int|bool $deleteEnabled
     * @return $this
     */
    public function deleteEnabled($deleteEnabled = true)
    {
        return $this->andWhere([$this->a('delete_enabled') => $deleteEnabled ? 1 : 0]);
    }

    /**
     * @param int|bool $switchEnabled
     * @return $this
     */
    public function switchEnabled($switchEnabled = true)
    {
        return $this->andWhere([$this->a('switch_enabled') => $switchEnabled ? 1 : 0]);
    }
}
