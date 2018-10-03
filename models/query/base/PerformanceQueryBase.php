<?php

namespace pvsaintpe\performance\models\query\base;

/**
 * This is the ActiveQuery class for [[\pvsaintpe\performance\models\Performance]].
 *
 * @see \pvsaintpe\performance\models\Performance
 */
class PerformanceQueryBase extends \pvsaintpe\search\components\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\Performance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\Performance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param integer|integer[] $id
     * @return $this
     */
    public function pk($id)
    {
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param integer|integer[] $id
     * @return $this
     */
    public function id($id)
    {
        return $this->andWhere([$this->a('id') => $id]);
    }

    /**
     * @param integer|integer[] $instancePerformanceId
     * @return $this
     */
    public function instancePerformanceId($instancePerformanceId)
    {
        return $this->andWhere([$this->a('instance_performance_id') => $instancePerformanceId]);
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
     * @param string|string[] $route
     * @param string|string[] $searchClass
     * @param integer|integer[] $merchantId
     * @param string|string[] $name
     * @return $this
     */
    public function routeSearchClassMerchantIdName($route, $searchClass, $merchantId, $name)
    {
        return $this->andWhere($this->a([
            'route' => $route,
            'search_class' => $searchClass,
            'merchant_id' => $merchantId,
            'name' => $name
        ]));
    }

    /**
     * @param string|string[] $route
     * @return $this
     */
    public function route($route)
    {
        return $this->andWhere([$this->a('route') => $route]);
    }

    /**
     * @param string|string[] $searchClass
     * @return $this
     */
    public function searchClass($searchClass)
    {
        return $this->andWhere([$this->a('search_class') => $searchClass]);
    }

    /**
     * @param string|string[] $name
     * @return $this
     */
    public function name($name)
    {
        return $this->andWhere([$this->a('name') => $name]);
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
     * @param int|bool $systemDefined
     * @return $this
     */
    public function systemDefined($systemDefined = true)
    {
        return $this->andWhere([$this->a('system_defined') => $systemDefined ? 1 : 0]);
    }
}
