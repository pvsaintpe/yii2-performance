<?php

namespace backend\modules\performance\models;

use backend\helpers\Html;
use backend\helpers\Serializer;
use common\models\PerformanceColumnSettings;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\performance\models\base\PerformanceColumnSettingsSearchBase;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * PerformanceColumnSettingsSearch represents the model behind the search form about `common\models\PerformanceColumnSettings`.
 */
class PerformanceColumnSettingsSearch extends PerformanceColumnSettingsSearchBase
{
    protected $paginationSize = 10;

    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [
            'performance_id',
            'required',
            'sort_strategy',
            'value',
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
            'attribute' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'attribute',
                'value' => function ($model) {
                    $searchClass= $model->performance->search_class;
                    $searchModel = new $searchClass;
                    return $searchModel->getAttributeLabel($model->attribute);
                }
            ],
            'value' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'value',
                'format' => 'raw',
                'value' => function (PerformanceColumnSettings $model) {
                    if ($model->type == 'select' && is_array($model->value) && count($model->value) > 0) {
                        /** @var \common\components\ActiveRecord $className */
                        $className = $model->relation_class;
                        return join(', ', $className::findFilterQuery(['id' => $model->value])->column());
                    }
                    return $model->value;
                }
            ],
            'order_position' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'order_position'
            ],
            'sort_strategy' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'sort_strategy',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->getAttribute('sort_strategy') == SORT_ASC) {
                        return '<span class="glyphicon glyphicon-sort-by-attributes" title="'.Yii::t('models', 'По возрастанию').'"></span>';
                    } elseif ($model->getAttribute('sort_strategy') == SORT_DESC) {
                        return '<span class="glyphicon glyphicon-sort-by-attributes-alt" title="'.Yii::t('models', 'По убыванию').'"></span>';
                    } else {
                        return null;
                    }
                }
            ],
            'required' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'required',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'action' => [
                'class' => 'backend\components\grid\ActionColumn',
                'template' => '{update} {delete}',
                'permissionPrefix' => $this->getPermissionPrefix(),
                'visibleButtons' => [
                    'delete' => function ($model, $key, $index) {
                        return $model->required === 1 ? false : true;
                    },
                ],
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data-pjax' => true,
                            'data-method' => 'POST',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'data-pjax' => true,
                            'data-method' => 'POST',
                            'class' => 'btn-main-modal'
                        ]);
                    },
                ],
                'deleteOptions' => [
                    'data-pjax' => true,
                    'data-method' => 'POST',
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

        $this->query = PerformanceColumnSettingsSearchBase::find();
        $this->query->andFilterWhere([
            $this->query->a('performance_id') => $this->performance_id,
            $this->query->a('order_position') => $this->order_position,
            $this->query->a('required') => $this->required,
            $this->query->a('sort_strategy') => $this->sort_strategy,
        ]);

        $this->query->andFilterWhere(['like', $this->query->a('attribute'), $this->attribute])
            ->andFilterWhere(['like', $this->query->a('value'), $this->value]);
        
        $this->query->innerJoinWith([
            'performance performance'
        ]);

        return $this->getDataProvider();
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return ArrayHelper::merge(
            parent::getSort(),
            [
                'defaultOrder' => [
                    'order_position' => SORT_ASC,
                ],
            ]
        );
    }
}
