<?php

namespace pvsaintpe\performance\models\base;

use Yii;
use pvsaintpe\performance\models\Performance;

/**
 * This is the model class for table "performance_admin_settings".
 *
 * @property integer $performance_id
 * @property integer $merchant_id
 * @property integer $is_default
 * @property integer $enabled
 * @property integer $order_position
 * @property string $expired_at
 * @property integer $admin_enabled
 * @property integer $view_enabled
 * @property integer $edit_enabled
 * @property integer $share_enabled
 * @property integer $delete_enabled
 * @property integer $switch_enabled
 *
 * @property Performance $performance
 * @property Performance $instancePerformance
 */
class PerformanceAdminSettingsBase extends \pvsaintpe\search\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'performance_admin_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'is_default',
                'enabled',
                'admin_enabled',
                'view_enabled',
                'edit_enabled',
                'share_enabled',
                'delete_enabled',
                'switch_enabled'
            ], 'filter', 'filter' => function ($value) {
                return $value ? 1 : 0;
            }, 'skipOnEmpty' => true],
            [[
                'is_default',
                'enabled',
                'admin_enabled',
                'view_enabled',
                'edit_enabled',
                'share_enabled',
                'delete_enabled',
                'switch_enabled'
            ], 'boolean'],
            [[
                'merchant_id',
                'order_position'
            ], 'integer', 'min' => 0],
            [['expired_at'], 'filter', 'filter' => function ($value) {
                return is_int($value) ? date('Y-m-d H:i:s', $value) : $value;
            }],
            [['expired_at'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['merchant_id'], 'required'],
            [['performance_id'], 'exist', 'skipOnError' => true, 'targetClass' => Performance::class, 'targetAttribute' => ['performance_id' => 'id']],
            [[
                'is_default',
                'admin_enabled',
                'view_enabled',
                'edit_enabled',
                'share_enabled',
                'delete_enabled',
                'switch_enabled'
            ], 'default', 'value' => '0'],
            [['enabled'], 'default', 'value' => '1'],
            [[
                'order_position',
                'expired_at'
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
            'merchant_id' => Yii::t('models', 'Мерчант'),
            'is_default' => Yii::t('models', 'По умолчанию'),
            'enabled' => Yii::t('models', 'Вкл.'),
            'order_position' => Yii::t('models', 'Порядок'),
            'expired_at' => Yii::t('models', 'Шаринг истекает в'),
            'admin_enabled' => Yii::t('models', 'Управление разрешено'),
            'view_enabled' => Yii::t('models', 'Просмотр разрешено'),
            'edit_enabled' => Yii::t('models', 'Изменение разрешено'),
            'share_enabled' => Yii::t('models', 'Шаринг разрешено'),
            'delete_enabled' => Yii::t('models', 'Удаление разрешено'),
            'switch_enabled' => Yii::t('models', 'Блокировка разрешено'),
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
     * @return \pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery(get_called_class());
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
            'is_default',
            'enabled',
            'admin_enabled',
            'view_enabled',
            'edit_enabled',
            'share_enabled',
            'delete_enabled',
            'switch_enabled'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function datetimeAttributes()
    {
        return ['expired_at'];
    }

    /**
     * @inheritdoc
     */
    public static function modelTitle()
    {
        return Yii::t('models', 'Настройки представлений');
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return [
            'performance_id',
            'merchant_id'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function titleKey()
    {
        return [
            'performance_id',
            'merchant_id'
        ];
    }

    /**
     * @inheritdoc
     */
    // public function getTitleText()
    // {
    //     return $this->performance_id . static::TITLE_SEPARATOR . $this->merchant_id;
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
