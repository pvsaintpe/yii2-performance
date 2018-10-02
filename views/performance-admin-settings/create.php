<?php

use backend\components\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\PerformanceAdminSettings */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('performance', 'Добавление доступа к представлению');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-danger performance-admin-settings-create">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
<?php
Pjax::end();