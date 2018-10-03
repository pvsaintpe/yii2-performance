<?php

use backend\helpers\Html;
use pvsaintpe\log\widgets\ActiveForm;
use common\models\Admin;

/* @var $this yii\web\View */
/* @var $model \pvsaintpe\performance\modules\performance\forms\PerformanceForm */
/* @var $form pvsaintpe\log\widgets\ActiveForm */

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validateOnChange' => false,
    'validateOnBlur' => true,
]); ?>
<div class="performance-form">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'order_position')->widget(\kartik\widgets\TouchSpin::class, [
        'pluginOptions' => [
            'verticalbuttons' => true,
            'min' => 0,
            'max' => 99999,
        ]
    ]); ?>
    <?= $form->field($model, 'system_defined')->dropDownList([
        0 => Yii::t('backend', 'Неактивно'),
        1 => Yii::t('backend', 'Активно')
    ]) ?>
    <?= $form->field($model, 'enabled')->dropDownList([
        0 => Yii::t('backend', 'Неактивно'),
        1 => Yii::t('backend', 'Активно')
    ]) ?>
    <?= $form->field($model, 'is_default')->dropDownList([
        0 => Yii::t('backend', 'Неактивно'),
        1 => Yii::t('backend', 'Активно')
    ]) ?>
</div>
<div class="box-footer">
    <?= Html::saveButton($model) ?>
</div>
<?php ActiveForm::end(); ?>
