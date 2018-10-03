<?php

use backend\helpers\Html;
use backend\widgets\GridView;
use backend\components\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel pvsaintpe\performance\modules\performance\models\PerformanceLanguageSettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('payment', 'Локализация представления');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-danger performance-language-settings-index">
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
                '/performance/performance-language-settings/index',
                't[performance_id]' => $searchModel->performance_id
            ]),
            'sorter' =>  [
                'class' => 'yii\widgets\LinkSorter',
                'sort'  => new \yii\data\Sort([
                    'route' =>  \yii\helpers\Url::to([
                        '/performance/performance-language-settings/index',
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
        &nbsp;
    </div>
</div>
<?php
$this->registerCss(<<<CSS
    .modal-dialog {
        width: 800px!important;
    }
CSS
);
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