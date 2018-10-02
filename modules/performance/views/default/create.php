<?php

use yii\web\View;
use backend\helpers\Html;
use backend\components\Pjax;
use backend\widgets\ActiveForm;
use backend\modules\performance\forms\PerformanceForm;

/** @var View $this */
/** @var PerformanceForm $model */

$this->title = Yii::t('performance', 'Создать представление');

Pjax::begin([
    'enablePushState' => false,
]);
?>
<div class="box box-danger performance-create">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
<?php
    Pjax::end();
?>