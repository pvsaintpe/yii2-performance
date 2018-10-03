<?php

namespace pvsaintpe\performance\models\base;

use Yii;
use pvsaintpe\performance\models\Performance;

/**
 * This is the model class for table "performance_language_settings".
 *
 * @property integer $performance_id
 * @property integer $language_id
 * @property string $name
 * @property string $description
 *
 * @property Performance $performance
 * @property Performance $instancePerformance
 */
class PerformanceLanguageSettingsBase extends \pvsaintpe\search\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'performance_language_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'performance_id',
                'language_id'
            ], 'integer', 'min' => 0],
            [['performance_id', 'language_id', 'name'], 'required'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['performance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performance::class, 'targetAttribute' => ['performance_id' => 'id']],
            [['description'], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'performance_id' => Yii::t('models', 'Представление'),
            'language_id' => Yii::t('models', 'Язык'),
            'name' => Yii::t('models', 'Название'),
            'description' => Yii::t('models', 'Описание'),
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
     * @return \pvsaintpe\performance\models\query\PerformanceLanguageSettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \pvsaintpe\performance\models\query\PerformanceLanguageSettingsQuery(get_called_class());
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
    public static function modelTitle()
    {
        return Yii::t('models', 'Локализация представлений');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return [
            'performance_id',
            'language_id'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return [
            'performance_id',
            'language_id'
        ];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->performance_id . static::TITLE_SEPARATOR . $this->language_id;
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
