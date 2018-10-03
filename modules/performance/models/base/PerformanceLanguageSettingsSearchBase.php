<?php

namespace pvsaintpe\performance\modules\performance\models\base;

use pvsaintpe\search\interfaces\SearchInterface;
use Yii;
use common\models\PerformanceLanguageSettings;
use backend\traits\SearchTrait;

/**
 * PerformanceLanguageSettingsSearchBase represents the model behind the search form about `common\models\PerformanceLanguageSettings`.
 */
class PerformanceLanguageSettingsSearchBase extends PerformanceLanguageSettings implements SearchInterface
{
    use SearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['performance_id'], 'integer'],
                [['language_id', 'name', 'description'], 'safe'],
            ]
        );
    }

    /**
     * @return string
     */
    public static function getGridTitle()
    {
        return Yii::t('performance', 'Локализация представлений');
    }

    /**
     * @return string
     */
    public function getListTitle()
    {
        return Yii::t('performance', 'Локализация представлений') . ': ' . $this->name;
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
            [
                'attribute' => 'language_id',
                'value' => function ($form, $widget) {
                    return ($model = $widget->model->language) ? $model->getDocName() : null;
                },
                'format' => 'raw',
            ],
            'name',
            'description',
        ];
    }
}
