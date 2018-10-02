<?php

namespace backend\modules\performance\models\base;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\PerformanceColumnSettings;
use yii\helpers\ArrayHelper;
use backend\helpers\Html;
use pvsaintpe\search\interfaces\SearchInterface;
use backend\traits\SearchTrait;
use backend\components\grid\CurrencyColumn;

/**
 * PerformanceColumnSettingsSearchBase represents the model behind the search form about `common\models\PerformanceColumnSettings`.
 */
class PerformanceColumnSettingsSearchBase extends PerformanceColumnSettings implements SearchInterface
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['performance_id', 'order_position', 'sort_strategy'], 'integer'],
                [['attribute', 'value'], 'safe'],
                [['required'], 'boolean'],
            ]
        );
    }

    /**
     * @return string
     */
    public static function getGridTitle()
    {
        return Yii::t('performance', 'Настройки полей представления');
    }

    /**
     * @return string
     */
    public function getListTitle()
    {
        return Yii::t('performance', 'Настройки полей представления') . ': ' . $this->performance_id;
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
                    return ($model = $widget->model->performance) ? $model->getTitleText() : null;
                },
                'format' => 'raw',
            ],
            'attribute',
            'value',
            'order_position',
            'required',
            'sort_strategy',
        ];
    }
}
