<?php

namespace pvsaintpe\performance\models;

use pvsaintpe\performance\helpers\Serializer;
use pvsaintpe\search\interfaces\SearchInterface;
use pvsaintpe\search\components\ActiveRecord;
use pvsaintpe\performance\models\base\PerformanceColumnSettingsBase;
use yii\helpers\Inflector;

/**
 * Performance column settings
 * @see \pvsaintpe\performance\models\query\PerformanceColumnSettingsQuery
 */
class PerformanceColumnSettings extends PerformanceColumnSettingsBase
{
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        if ($insert) {
            $performance = Performance::find()->id($this->performance_id)->one();
            $searchClass = $performance->search_class;

            /** @var SearchInterface|ActiveRecord $searchModel */
            $searchModel = new $searchClass();
            $relationKey = lcfirst(Inflector::id2camel(str_replace('_id', '', $this->attribute), '_'));
            $relationClass = null;
            if (isset($searchModel::singularRelations()[$relationKey])) {
                $relationClass = $searchModel::singularRelations()[$relationKey]['class'];
            }

            $tableSchema = $searchModel::getDb()->getTableSchema($searchModel::tableName());
            if ($columnSchema = $tableSchema->getColumn($this->attribute)) {
                if ($columnSchema->phpType == 'string') {
                    $this->type = 'string';
                    if ($columnSchema->type == 'text') {
                        $this->type = 'text';
                    }
                    if ($columnSchema->type == 'decimal') {
                        $this->type = 'decimal';
                    }
                }
                if ($columnSchema->phpType == 'integer') {
                    $this->type = 'integer';
                }
                if ($columnSchema->phpType == 'double') {
                    $this->type = 'double';
                }
            }

            if (in_array($this->attribute, $searchModel::booleanAttributes())) {
                $this->type = 'boolean';
            }

            if (in_array($this->attribute, $searchModel::dateAttributes())) {
                $this->type = 'date';
            }

            if (in_array($this->attribute, $searchModel::datetimeAttributes())) {
                $this->type = 'datetime';
            }

            if ($relationClass) {
                $this->relation_class = $relationClass;
                $this->relation_key = $relationKey;
                $this->type = 'select';
            }

            if (!$this->sort_strategy) {
                $sort = property_exists($searchModel, 'getSort') ? $searchModel->getSort() : false;
                if ($sort && isset($sort['defaultOrder']) && isset($sort['defaultOrder'][$this->attribute])) {
                    $this->sort_strategy = $sort['defaultOrder'][$this->attribute];
                }
            }

            $this->own_attribute = $searchModel->isAttributeSafe($this->attribute);
            $this->protected = in_array($this->attribute, [
                'credentials',
                'secret_key',
                'head',
                'body',
            ]);

            if (in_array($this->attribute, $searchModel::primaryKey())) {
                $this->required = 1;
            }
        }

        return true;
    }

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            if ((is_array($this->value) && count($this->value) > 0)
                || (is_numeric($this->value) && $this->value >= 0)
                || (is_string($this->value) && strlen($this->value) > 0)
            ) {
                $this->value = serialize($this->value);
            } else {
                $this->value = null;
            }
            return true;
        }
        return false;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!is_null($this->value) && Serializer::isSerialized($this->value)) {
            $this->value = unserialize($this->value);
        }
    }
}
