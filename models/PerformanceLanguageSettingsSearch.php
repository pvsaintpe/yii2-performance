<?php

namespace backend\modules\performance\models;

use backend\helpers\Html;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\performance\models\base\PerformanceLanguageSettingsSearchBase;
use yii\helpers\ArrayHelper;

/**
 * PerformanceLanguageSettingsSearch represents the model behind the search form about `common\models\PerformanceLanguageSettings`.
 */
class PerformanceLanguageSettingsSearch extends PerformanceLanguageSettingsSearchBase
{
    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [
            'performance_id'
        ];
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [
            'performance_id' => [
                'class' => 'backend\components\grid\PerformanceIdColumn',
                'customFilters' => $this->getFilter('performance_id'),
            ],
            'language_id' => [
                'class' => 'backend\components\grid\LanguageIdColumn',
                'customFilters' => $this->getFilter('language_id'),
            ],
            'name' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'name'
            ],
            'description' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'description'
            ],
            'action' => [
                'class' => 'backend\components\grid\ActionColumn',
                'permissionPrefix' => $this->getPermissionPrefix(),
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'data-pjax' => true,
                            'data-method' => 'POST',
                            'class' => 'btn-main-modal'
                        ]);
                    },
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = null)
    {
        if (!empty($params)) {
            $this->load($params);
        }

        $this->query = PerformanceLanguageSettingsSearchBase::find();
        $this->query->andFilterWhere([
            $this->query->a('performance_id') => $this->performance_id,
        ]);

        $this->query->andFilterWhere(['like', $this->query->a('language_id'), $this->language_id])
            ->andFilterWhere(['like', $this->query->a('name'), $this->name])
            ->andFilterWhere(['like', $this->query->a('description'), $this->description]);
        
        $this->query->innerJoinWith([
            'language language',
            'performance performance'
        ]);

        return $this->getDataProvider();
    }
}
