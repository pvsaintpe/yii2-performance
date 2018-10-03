<?php

namespace pvsaintpe\performance\modules\performance\models;

use backend\helpers\Html;
use backend\helpers\Serializer;
use common\models\query\PerformanceAdminSettingsQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use pvsaintpe\performance\modules\performance\models\base\PerformanceSearchBase;
use yii\helpers\ArrayHelper;

/**
 * PerformanceSearch represents the model behind the search form about `common\models\Performance`.
 */
class PerformanceSearch extends PerformanceSearchBase
{
    /**
     * @return array
     */
    public function getGridToolbar()
    {
        return [
            $this->getGridReset(),
        ];
    }

    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [
            'merchant_id',
            'search_class',
            'route',
        ];
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [
            'id' => [
                'class' => 'backend\components\grid\IdColumn',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'merchant_id' => [
                'class' => 'backend\components\grid\MerchantIdColumn',
                'customFilters' => $this->getFilter('merchant_id'),
            ],
            'search_class' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'search_class'
            ],
            'route' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'route'
            ],
            'name' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->url_params && Serializer::isSerialized($model->url_params)) {
                        $urlParams = unserialize($model->url_params);
                        if (!empty($urlParams)) {
                            return $model->name;
                        }
                    }
                    return Html::a(
                        $model->name,
                        ['/' . $model->route, 't[performance_id]' => $model->id],
                        ['data-pjax' => 0]
                    );
                }
            ],
            'order_position' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'order_position'
            ],
            'default_state' => [
                'class' => 'backend\components\grid\PerformanceAdminDefaultColumn',
                'attribute' => 'default_state',
                'permissionPrefix' => 'performance/performance-admin-settings/',
                'value' => function (PerformanceSearchBase $model) {
                    return $model->isDefault();
                }
            ],
            'enabled_state' => [
                'class' => 'backend\components\grid\PerformanceAdminEnabledColumn',
                'attribute' => 'enabled_state',
                'permissionPrefix' => 'performance/performance-admin-settings/',
                'value' => function (PerformanceSearchBase $model) {
                    return $model->isEnabled();
                }
            ],
            'action' => [
                'class' => 'backend\components\grid\DataColumn',
                'header' => Yii::t('models', 'Действия'),
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'width:90px;'
                ],
                'value' => function (PerformanceSearchBase $model) {
                    $buttons = [];

                    if ($model->allowedEdit()) {
                        $buttons[] = Html::a(
                            '<i class="glyphicon glyphicon-pencil"></i>',
                            ['update', 'id' => $model->id],
                            ['class' => 'btn btn-primary btn-xs btn-main-modal', 'data-pjax' => 0]
                        );
                    }

                    if ($model->allowedAdmin()) {
                        $buttons[] = Html::a(
                            '<i class="glyphicon glyphicon-cog"></i>',
                            [
                                '/performance/performance-column-settings/index',
                                PerformanceColumnSettingsSearch::getFormName() . '[performance_id]' => $model->id
                            ],
                            ['class' => 'btn btn-warning btn-xs btn-main-modal', 'data-pjax' => 0]
                        );
                    }

                    if ($model->allowedDelete()) {
                        $buttons[] = Html::a(
                            '<i class="glyphicon glyphicon-remove"></i>',
                            ['delete', 'id' => $model->id],
                            ['class' => 'btn btn-danger btn-xs', 'data-pjax' => 0]
                        );
                    }

                    return join(' ', $buttons);
                }
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

        $this->query = PerformanceSearchBase::find();
        $this->query->andFilterWhere([
            $this->query->a('id') => $this->id,
            $this->query->a('merchant_id') => $this->merchant_id,
            $this->query->a('order_position') => $this->order_position,
        ]);

        $this->query->innerJoinWith([
            'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                $performanceAdminSettingsQuery->andFilterWhere([
                    $this->query->a('is_default') => $this->default_state,
                    $this->query->a('enabled') => $this->enabled_state,
                ]);
            }
        ]);

        $this->query->andFilterWhere(['like', $this->query->a('search_class'), $this->search_class])
            ->andFilterWhere(['like', $this->query->a('route'), $this->route])
            ->andFilterWhere(['like', $this->query->a('name'), $this->name]);
        
        $this->query->innerJoinWith([
            'merchant merchant'
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
                    'id' => SORT_DESC,
                    'order_position' => SORT_ASC,
                ],
            ]
        );
    }
}
