<?php

use backend\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Language;
use common\models\Performance;

/* @var $this yii\web\View */
/* @var $model common\models\PerformanceLanguageSettings */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'id' => 'w' . time(),
    'options' => [
        'data-pjax' => true,
        'data-method' => 'POST',
    ],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false
]); ?>
<div class="performance-language-settings-form">
    <?= $form->field($model, 'language_id')->widget(\kartik\widgets\Select2::class, [
        'data' => Language::findForFilter(), 
        'options' => ['placeholder' => Yii::t('performance', 'Язык')],
        'pluginOptions' => [
            'allowClear' => true,
            'disabled' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

</div>
<div class="box-footer">
    <?= Html::saveButton($model) ?>

    </div>
<?php ActiveForm::end(); ?>
