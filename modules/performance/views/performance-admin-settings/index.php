<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use backend\components\Pjax;
use common\models\Admin;
use common\models\PerformanceAdminSettings;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\performance\models\PerformanceAdminSettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('payment', 'Расшарить представление');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-danger performance-admin-settings-index">
    <div class="box-body">
        <?= GridView::widget([
            'id' => 'w' . time(),
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'enablePushState' => false,
                    'enableReplaceState' => false,
                ],
            ],
            'filterUrl' => \yii\helpers\Url::to([
                '/performance/performance-admin-settings/index',
                't[performance_id]' => $searchModel->performance_id
            ]),
            'sorter' =>  [
                'class' => 'yii\widgets\LinkSorter',
                'sort'  => new \yii\data\Sort([
                    'route' =>  \yii\helpers\Url::to([
                        '/performance/performance-admin-settings/index',
                        't[performance_id]' => $searchModel->performance_id
                    ])
                ]),
                'options' => [
                    'data-method' => 'POST',
                    'data-pjax' => 1,
                ]
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'disableColumns' => $searchModel->getDisableColumns(),
            'columns' => $searchModel->getGridColumns(),
            'toolbar' => $searchModel->getGridToolbar(),
            'panelBeforeTemplate' => '',
        ]) ?>
    </div>
    <div id="box-footer-addons">
        <?php
            if (Admin::findFilterQuery([
                    [
                        'NOT',
                        [
                            'id' => PerformanceAdminSettings::find()
                                ->performanceId($searchModel->performance_id)
                                ->select('merchant_id')
                                ->column()
                        ]
                    ],
                    [
                        'NOT',
                        [
                            'username' => [
                                'admin',
                                'root',
                            ]
                        ]
                    ],
                    'enabled' => 1
                ])->count()
            ) {
                ?>
                <?= Html::a(
                    Yii::t('models', 'Добавить оператора'),
                    [
                        'create',
                        'id' => $searchModel->performance_id,
                        't[performance_id]' => $searchModel->performance_id,
                    ],
                    [
                        'class' => 'btn btn-success btn-sm btn-main-modal',
                        'data-pjax' => 1,
                        'data-method' => "POST",
                    ]
                ) ?>
                <?php
            }
        ?>
    </div>
</div>
<?php
//$this->registerCss(<<<CSS
//    .modal-dialog {
//        width: 800px!important;
//    }
//CSS
//);
$timestamp = time();
$this->registerJs(<<<JS
$(document).ready(function() {
   var modal_{$timestamp} = $('#main-modal');
   var modalTools_{$timestamp} = modal_{$timestamp}.find('.modal-tools').html('');
   var boxFooter_{$timestamp} = $('#box-footer-addons');
   modalTools_{$timestamp}.html(boxFooter_{$timestamp}.html());
   boxFooter_{$timestamp}.remove();
});
JS
, \backend\components\View::POS_END);
Pjax::end();