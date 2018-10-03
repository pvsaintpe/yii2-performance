<?php

namespace pvsaintpe\performance\models\base;

use Yii;
use pvsaintpe\performance\models\Performance;

/**
 * This is the model class for table "performance_column_settings".
 *
 * @property integer $performance_id
 * @property string $attribute
 * @property integer $own_attribute
 * @property string $type
 * @property string $value
 * @property integer $values_hidden
 * @property integer $order_position
 * @property integer $required
 * @property integer $protected
 * @property integer $hidden
 * @property integer $sort_strategy
 * @property string $relation_class
 * @property string $relation_key
 * @property string $options
 * @property string $title
 * @property integer $export_enabled
 *
 * @property Performance $performance
 * @property Performance $instancePerformance
 */
class PerformanceColumnSettingsBase extends \pvsaintpe\search\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'performance_column_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'own_attribute',
                'values_hidden',
                'required',
                'protected',
                'hidden',
                'export_enabled'
            ], 'filter', 'filter' => function ($value) {
                return $value ? 1 : 0;
            }, 'skipOnEmpty' => true],
            [[
                'own_attribute',
                'values_hidden',
                'required',
                'protected',
                'hidden',
                'export_enabled'
            ], 'boolean'],
            [[
                'order_position',
                'sort_strategy'
            ], 'integer', 'min' => 0],
            [['attribute'], 'required'],
            [['options'], 'string'],
            [['attribute'], 'string', 'max' => 120],
            [['type'], 'string', 'max' => 45],
            [['value', 'relation_class', 'relation_key', 'title'], 'string', 'max' => 255],
            [['performance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performance::class, 'targetAttribute' => ['performance_id' => 'id']],
            [[
                'own_attribute',
                'required',
                'protected',
                'hidden'
            ], 'default', 'value' => '0'],
            [['type'], 'default', 'value' => 'raw'],
            [[
                'values_hidden',
                'export_enabled'
            ], 'default', 'value' => '1'],
            [[
                'value',
                'order_position',
                'sort_strategy',
                'relation_class',
                'relation_key',
                'options',
                'title'
            ], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'performance_id' => Yii::t('models', 'Представление'),
            'attribute' => Yii::t('models', 'Аттрибут'),
            'own_attribute' => Yii::t('models', 'Родной аттрибут'),
            'type' => Yii::t('models', 'Тип'),
            'value' => Yii::t('models', 'Значение по умолчанию'),
            'values_hidden' => Yii::t('models', 'Значения скрыты'),
            'order_position' => Yii::t('models', 'Порядок'),
            'required' => Yii::t('models', 'Обязательное'),
            'protected' => Yii::t('models', 'Защищенное'),
            'hidden' => Yii::t('models', 'Скрытое'),
            'sort_strategy' => Yii::t('models', 'Сортировка по умолчанию'),
            'relation_class' => Yii::t('models', 'Класс'),
            'relation_key' => Yii::t('models', 'Связь'),
            'options' => Yii::t('models', 'Опции'),
            'title' => Yii::t('models', 'Описание'),
            'export_enabled' => Yii::t('models', 'Разрешено для экспорта'),
        ];
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceQuery|\yii\db\ActiveQuery
     */
    public function getPerformance()
    {
        return $this->hasOne(Performance::class, ['id' => 'performance_id']);
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceQuery|\yii\db\ActiveQuery
     */
    public function getInstancePerformance()
    {
        return $this->hasOne(Performance::class, ['id' => 'instance_performance_id'])
            ->viaTable('performance via_performance', ['id' => 'performance_id']);
    }

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\query\PerformanceColumnSettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \pvsaintpe\performance\models\query\PerformanceColumnSettingsQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function singularRelations()
    {
        return [
            'performance' => [
                'hasMany' => false,
                'class' => 'pvsaintpe\performance\models\Performance',
                'link' => ['id' => 'performance_id'],
                'direct' => true,
                'viaTable' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function booleanAttributes()
    {
        return [
            'own_attribute',
            'values_hidden',
            'required',
            'protected',
            'hidden',
            'export_enabled'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Настройки полей представления');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return [
            'performance_id',
            'attribute'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return [
            'performance_id',
            'attribute'
        ];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->performance_id . static::TITLE_SEPARATOR . $this->attribute;
    // }

    /**
     * @param string|array|\yii\db\Expression $condition
     * @param array $params
     * @param string|array|\yii\db\Expression $orderBy
     * @return array
     */
    public function performanceIdListItems($condition = null, $params = [], $orderBy = null)
    {
        return Performance::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|\yii\db\Expression $orderBy
     * @return array
     */
    public function performanceIdFilterListItems(array $condition = [], $orderBy = null)
    {
        return Performance::findFilterListItems($condition, $orderBy);
    }
}
