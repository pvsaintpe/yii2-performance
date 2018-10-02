<?php

use backend\helpers\Html;
use backend\components\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Performance */

$this->title = Yii::t('performance', 'Редактировать представление');

Pjax::begin([
    'enablePushState' => false,
]);
?>
<div class="box box-danger performance-update">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
<?php
    Pjax::end();
?>