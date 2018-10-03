<?php

use backend\components\Pjax;
use backend\widgets\GridView;
use backend\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel pvsaintpe\performance\modules\performance\models\PerformanceColumnSettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('payment', 'Настроить представление');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box box-danger performance-column-settings-index">
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
                '/performance/performance-column-settings/index',
                't[performance_id]' => $searchModel->performance_id
            ]),
            'sorter' =>  [
                'class' => 'yii\widgets\LinkSorter',
                'sort'  => new \yii\data\Sort([
                    'route' =>  \yii\helpers\Url::to([
                        '/performance/performance-column-settings/index',
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
            'panelBeforeTemplate' => '',
            'rowOptions' => function (\common\models\PerformanceColumnSettings $model) {
                if ($model->hidden) {
                    return ['class' => 'inactive-row'];
                }
            },
        ]) ?>
    </div>
    <div id="box-footer-addons">
        <?= Html::a(
            Yii::t('models', 'Добавить столбец'),
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
    </div>
</div>
<?php

if ($reload) {
$this->registerJs(<<<JS
    $.pjax.reload({container: '#w0-pjax', timeout: false})
JS
    , \backend\components\View::POS_END);
}

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