<?php

use backend\widgets\ActiveForm;
use common\models\Performance;

/** @var \backend\traits\SearchTrait|\common\components\ActiveRecord $model */

$filters = [];

$form = ActiveForm::begin([
    'id' => 'performance-filters',
    'method' => 'GET',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => \yii\helpers\Url::to(['/payment/default/index']),
]);

echo $form->field($model, 'performance_id')->widget(\kartik\widgets\Select2::class, [
    'data' => Performance::findForFilter($model->getPerformanceFilters()),
    'options' => [
        'placeholder' => Yii::t('layout', 'Сохраненные представления'),
        'multiple' => false,
    ],
    'value' => null,
    'showToggleAll' => true,
    'pluginOptions' => [
        'allowClear' => true,
        'width' => '250px'
    ],
    'pjaxContainerId' => 'w0-pjax'
])->label(false);

ActiveForm::end();

$modelName = strtolower($model->formName());

$this->registerJs(<<<JS
    jQuery(document).on('ready pjax:success', function() {
        jQuery('#{$modelName}-performance_id').change(function(){
            jQuery('#performance-filters').submit();
        });
    });
    jQuery('#{$modelName}-performance_id').change(function(){
        jQuery('#performance-filters').submit();
    });
JS
);