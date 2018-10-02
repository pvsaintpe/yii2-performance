<?php

use backend\helpers\Html;
use backend\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\modules\performance\models\base\PerformanceSearchBase */

$this->title = $model->getListTitle();
?>
<div class="box box-danger performance-view">
    <div class="box-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => $model->getListColumns(),
            'disableAttributes' => $model->getDisableAttributes(),
        ])?>
    </div>
</div>