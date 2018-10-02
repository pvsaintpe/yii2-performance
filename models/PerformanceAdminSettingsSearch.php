<?php

namespace backend\modules\performance\models;

use common\models\PerformanceAdminSettings;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\performance\models\base\PerformanceAdminSettingsSearchBase;
use backend\helpers\Html;

/**
 * PerformanceAdminSettingsSearch represents the model behind the search form about `common\models\PerformanceAdminSettings`.
 */
class PerformanceAdminSettingsSearch extends PerformanceAdminSettingsSearchBase
{
    /**
     * @return array
     */
    public function getDisableColumns()
    {
        return [
            'performance_id',
            'expired_at',
            'admin_enabled',
            'view_enabled',
            'edit_enabled',
            'delete_enabled',
            'switch_enabled',
            'share_enabled'
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
            'merchant_id' => [
                'class' => 'backend\components\grid\MerchantIdColumn',
                'customFilters' => $this->getFilter('merchant_id'),
            ],
            'expired_at' => [
                'class' => 'backend\components\grid\DatetimeRangeColumn',
                'attribute' => 'expired_at',
            ],
            'admin_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'admin_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'view_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'view_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'edit_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'edit_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'share_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'share_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'delete_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'delete_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'switch_enabled' => [
                'class' => 'backend\components\grid\BooleanColumn',
                'attribute' => 'switch_enabled',
                'permissionPrefix' => $this->getPermissionPrefix(),
            ],
            'chmod' => [
                'class' => 'backend\components\grid\DataColumn',
                'attribute' => 'chmod',
                'format' => 'raw',
                'value' => function (PerformanceAdminSettingsSearchBase $model) {
                    return join(' ', array_filter([
                        $model->admin_enabled ? '<small title="' . Yii::t('models', 'Управление') . '" style="margin-right:5px;" class="label pull-left bg-green">A</small>' : '',
                        $model->edit_enabled ? '<small title="' . Yii::t('models', 'Изменение') . '"  style="margin-right:5px;"  class="label pull-left bg-blue">E</small>' : '',
                        $model->view_enabled ? '<small title="' . Yii::t('models', 'Просмотр') . '"  style="margin-right:5px;"  class="label pull-left bg-maroon">V</small>' : '',
                        $model->share_enabled ? '<small title="' . Yii::t('models', 'Шаринг') . '"  style="margin-right:5px;"  class="label pull-left bg-purple">SH</small>' : '',
                        $model->delete_enabled ? '<small title="' . Yii::t('models', 'Удаление') . '"  style="margin-right:5px;"  class="label pull-left bg-black">D</small>' : '',
                        $model->switch_enabled ? '<small title="' . Yii::t('models', 'Блокировка') . '"  style="margin-right:5px;"  class="label pull-left bg-gray">SW</small>' : '',
                    ]));
                }
            ],
            'action' => [
                'class' => 'backend\components\grid\ActionColumn',
                'permissionPrefix' => $this->getPermissionPrefix(),
                'visibleButtons' => [
                    'delete' => function (PerformanceAdminSettingsSearchBase $model) {
                        return $model->allowedShare() || $model->allowedAdmin();
                    },
                    'update' => function (PerformanceAdminSettingsSearchBase $model) {
                        return $model->allowedShare() || $model->allowedAdmin();
                    }
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

        $this->query = PerformanceAdminSettingsSearchBase::find();
        
        $this->initDateFilters();
        $this->initDatetimeFilters();

        $this->query->andFilterWhere([
            $this->query->a('performance_id') => $this->performance_id,
            $this->query->a('merchant_id') => $this->merchant_id,
            $this->query->a('admin_enabled') => $this->admin_enabled,
            $this->query->a('view_enabled') => $this->view_enabled,
            $this->query->a('edit_enabled') => $this->edit_enabled,
            $this->query->a('share_enabled') => $this->share_enabled,
            $this->query->a('delete_enabled') => $this->delete_enabled,
            $this->query->a('switch_enabled') => $this->switch_enabled,
        ]);
        
        $this->query->innerJoinWith([
            'merchant merchant',
            'performance performance'
        ]);
        return $this->getDataProvider();
    }
}
