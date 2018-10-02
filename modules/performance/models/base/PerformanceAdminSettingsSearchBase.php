<?php

namespace backend\modules\performance\models\base;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PerformanceAdminSettings;
use yii\helpers\ArrayHelper;
use backend\helpers\Html;
use pvsaintpe\search\interfaces\SearchInterface;
use backend\traits\SearchTrait;
use backend\components\grid\CurrencyColumn;

/**
 * PerformanceAdminSettingsSearchBase represents the model behind the search form about `common\models\PerformanceAdminSettings`.
 */
class PerformanceAdminSettingsSearchBase extends PerformanceAdminSettings implements SearchInterface
{
    use SearchTrait;

    public $chmod;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['performance_id', 'merchant_id'], 'integer'],
                [['expired_at'], 'filter', 'filter' => 'trim'],
                [['expired_at'], 'date', 'format' => 'dd/MM/YYYY - dd/MM/YYYY', 'message' => Yii::t('backend', 'Некорректный диапазон дат')],
                [['admin_enabled', 'view_enabled', 'edit_enabled', 'share_enabled', 'delete_enabled', 'switch_enabled'], 'boolean'],
                [['chmod'], 'safe']
            ]
        );
    }

    /**
     * @return string
     */
    public static function getGridTitle()
    {
        return Yii::t('performance', 'Настройки представлений');
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'chmod' => Yii::t('models', 'Привилегии'),
            ]
        );
    }

    /**
     * @return string
     */
    public function getListTitle()
    {
        return Yii::t('performance', 'Настройки представлений') . ': ' . $this->performance_id;
    }

    /**
     * @return array
     */
    public function getListColumns()
    {
        return [
            [
                'attribute' => 'performance_id',
                'value' => function ($form, $widget) {
                    return ($model = $widget->model->performance) ? $model->getDocName() : null;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'merchant_id',
                'value' => function ($form, $widget) {
                    return ($model = $widget->model->merchant) ? $model->getDocName() : null;
                },
                'format' => 'raw',
            ],
            'expired_at',
            'admin_enabled',
            'view_enabled',
            'edit_enabled',
            'share_enabled',
            'delete_enabled',
            'switch_enabled',
        ];
    }
}
