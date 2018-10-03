<?php

namespace pvsaintpe\performance\models\base;

use Yii;
use pvsaintpe\performance\models\Performance;
use pvsaintpe\performance\models\PerformanceAdminSettings;
use pvsaintpe\performance\models\PerformanceColumnSettings;
use pvsaintpe\performance\models\PerformanceLanguageSettings;

/**
 * This is the model class for table "performance".
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $instance_performance_id
 * @property string $search_class
 * @property string $route
 * @property string $url_params
 * @property string $query_params
 * @property string $name
 * @property integer $order_position
 * @property integer $enabled
 * @property integer $system_defined
 *
 * @property Performance $instancePerformance
 * @property Performance[] $performances
 * @property PerformanceAdminSettings[] $performanceAdminSettings
 * @property PerformanceColumnSettings[] $performanceColumnSettings
 * @property PerformanceLanguageSettings[] $performanceLanguageSettings
 */
class PerformanceBase extends \pvsaintpe\search\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'performance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'enabled',
                'system_defined'
            ], 'filter', 'filter' => function ($value) {
                return $value ? 1 : 0;
            }, 'skipOnEmpty' => true],
            [[
                'enabled',
                'system_defined'
            ], 'boolean'],
            [[
                'merchant_id',
                'instance_performance_id',
                'order_position'
            ], 'integer', 'min' => 0],
            [['merchant_id', 'name'], 'required'],
            [['search_class', 'route', 'url_params', 'query_params'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 120],
            [['route', 'search_class', 'merchant_id', 'name'], 'unique', 'targetAttribute' => ['route', 'search_class', 'merchant_id', 'name'], 'message' => 'The combination of Мерчант, Search-модель, URL грида and Название has already been taken.'],
            [['instance_performance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performance::class, 'targetAttribute' => ['instance_performance_id' => 'id']],
            [['enabled'], 'default', 'value' => '1'],
            [['system_defined'], 'default', 'value' => '0'],
            [[
                'instance_performance_id',
                'search_class',
                'route',
                'url_params',
                'query_params',
                'order_position'
            ], 'default', 'value' => null],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('models', 'ID'),
            'merchant_id' => Yii::t('models', 'Мерчант'),
            'instance_performance_id' => Yii::t('models', 'Исходное представление'),
            'search_class' => Yii::t('models', 'Search-модель'),
            'route' => Yii::t('models', 'URL грида'),
            'url_params' => Yii::t('models', 'Параметры URL'),
            'query_params' => Yii::t('models', 'Аргументы'),
            'name' => Yii::t('models', 'Название'),
            'order_position' => Yii::t('models', 'Порядок'),
            'enabled' => Yii::t('models', 'Вкл.'),
            'system_defined' => Yii::t('models', 'Системное'),
        ];
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceQuery|\yii\db\ActiveQuery
     */
    public function getInstancePerformance()
    {
        return $this->hasOne(Performance::class, ['id' => 'instance_performance_id']);
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceQuery|\yii\db\ActiveQuery
     */
    public function getPerformances()
    {
        return $this->hasMany(Performance::class, ['instance_performance_id' => 'id']);
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery|\yii\db\ActiveQuery
     */
    public function getPerformanceAdminSettings()
    {
        return $this->hasMany(PerformanceAdminSettings::class, ['performance_id' => 'id']);
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceColumnSettingsQuery|\yii\db\ActiveQuery
     */
    public function getPerformanceColumnSettings()
    {
        return $this->hasMany(PerformanceColumnSettings::class, ['performance_id' => 'id']);
    }

    /**
     * @return \pvsaintpe\performance\models\query\PerformanceLanguageSettingsQuery|\yii\db\ActiveQuery
     */
    public function getPerformanceLanguageSettings()
    {
        return $this->hasMany(PerformanceLanguageSettings::class, ['performance_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \pvsaintpe\performance\models\query\PerformanceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \pvsaintpe\performance\models\query\PerformanceQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function singularRelations()
    {
        return [
            'instancePerformance' => [
                'hasMany' => false,
                'class' => 'pvsaintpe\performance\models\Performance',
                'link' => ['id' => 'instance_performance_id'],
                'direct' => true,
                'viaTable' => false
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function pluralRelations()
    {
        return [
            'performances' => [
                'hasMany' => true,
                'class' => 'pvsaintpe\performance\models\Performance',
                'link' => ['instance_performance_id' => 'id'],
                'direct' => false,
                'viaTable' => false
            ],
            'performanceAdminSettings' => [
                'hasMany' => true,
                'class' => 'pvsaintpe\performance\models\PerformanceAdminSettings',
                'link' => ['performance_id' => 'id'],
                'direct' => false,
                'viaTable' => false
            ],
            'performanceColumnSettings' => [
                'hasMany' => true,
                'class' => 'pvsaintpe\performance\models\PerformanceColumnSettings',
                'link' => ['performance_id' => 'id'],
                'direct' => false,
                'viaTable' => false
            ],
            'performanceLanguageSettings' => [
                'hasMany' => true,
                'class' => 'pvsaintpe\performance\models\PerformanceLanguageSettings',
                'link' => ['performance_id' => 'id'],
                'direct' => false,
                'viaTable' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function booleanAttributes()
    {
        return [
            'enabled',
            'system_defined'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Представления');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return [
            'route',
            'search_class',
            'merchant_id',
            'name'
        ];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->route . static::TITLE_SEPARATOR . $this->search_class . static::TITLE_SEPARATOR . $this->merchant_id . static::TITLE_SEPARATOR . $this->name;
    // }

    /**
     * @param array $config
     * @return Performance
     */
    public function newPerformance(array $config = [])
    {
        $model = new Performance($config);
        $model->instance_performance_id = $this->id;
        return $model;
    }

    /**
     * @param array $config
     * @return PerformanceAdminSettings
     */
    public function newPerformanceAdminSetting(array $config = [])
    {
        $model = new PerformanceAdminSettings($config);
        $model->performance_id = $this->id;
        return $model;
    }

    /**
     * @param array $config
     * @return PerformanceColumnSettings
     */
    public function newPerformanceColumnSetting(array $config = [])
    {
        $model = new PerformanceColumnSettings($config);
        $model->performance_id = $this->id;
        return $model;
    }

    /**
     * @param array $config
     * @return PerformanceLanguageSettings
     */
    public function newPerformanceLanguageSetting(array $config = [])
    {
        $model = new PerformanceLanguageSettings($config);
        $model->performance_id = $this->id;
        return $model;
    }

    /**
     * @param string|array|\yii\db\Expression $condition
     * @param array $params
     * @param string|array|\yii\db\Expression $orderBy
     * @return array
     */
    public function instancePerformanceIdListItems($condition = null, $params = [], $orderBy = null)
    {
        return Performance::findListItems($condition, $params, $orderBy);
    }

    /**
     * @param array $condition
     * @param string|array|\yii\db\Expression $orderBy
     * @return array
     */
    public function instancePerformanceIdFilterListItems(array $condition = [], $orderBy = null)
    {
        return Performance::findFilterListItems($condition, $orderBy);
    }
}
