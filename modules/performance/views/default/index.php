<?php

use backend\helpers\Html;
use backend\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\performance\models\PerformanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $permissionPrefix */

$this->title = $searchModel::getGridTitle();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-danger performance-index">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'disableColumns' => $searchModel->getDisableColumns(),
            'columns' => $searchModel->getGridColumns(),
            'toolbar' => $searchModel->getGridToolbar(),
        ]) ?>
    </div>
</div>