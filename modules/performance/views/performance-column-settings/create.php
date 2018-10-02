<?php

use backend\helpers\Html;
use backend\components\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\PerformanceColumnSettings */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('performance', 'Добавление столбца');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="box box-danger performance-column-settings-create">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
<?php
//$this->registerCss(<<<CSS
//.modal-dialog {
//    width: 900px!important;
//}
//CSS
//);

Pjax::end();