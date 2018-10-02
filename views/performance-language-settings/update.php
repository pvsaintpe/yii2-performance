<?php

use backend\helpers\Html;
use backend\components\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\PerformanceLanguageSettings */

Pjax::begin([
    'enablePushState' => false,
    'enableReplaceState' => false,
    'id' => 'w' . time() . '-pjax',
]);

$this->title = Yii::t('performance', 'Редактирование шаблона');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box box-danger performance-language-settings-update">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
<?php
$this->registerCss(<<<CSS
    .modal-dialog {
        width: 800px!important;
    }
CSS
);
Pjax::end();