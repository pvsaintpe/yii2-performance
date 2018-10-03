<?php

use backend\helpers\Html;
use pvsaintpe\log\widgets\ActiveForm;
use common\models\Admin;
use common\models\Performance;
use common\models\PerformanceAdminSettings;

/* @var $this yii\web\View */
/* @var $model \pvsaintpe\performance\modules\performance\forms\PerformanceShareForm */
/* @var $form pvsaintpe\log\widgets\ActiveForm */

$form = ActiveForm::begin([
    'id' => 'w' . time(),
    'options' => [
        'data-pjax' => true,
        'data-method' => 'POST',
    ],
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false
]);
?>
<div class="performance-admin-settings-form">
    <?php if ($model->getIsNewRecord()) { ?>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'merchant_id')->widget(\kartik\widgets\Select2::class, [
                    'data' => Admin::findForFilter([
                        [
                            'NOT',
                            [
                                'id' => PerformanceAdminSettings::find()
                                    ->performanceId($model->getPerformance()->id)
                                    ->select('merchant_id')
                                    ->column()
                            ]
                        ],
                        [
                            'NOT',
                            [
                                'username' => [
                                    'admin',
                                    'root',
                                ]
                            ]
                        ],
                        'enabled' => 1
                    ]),
                    'options' => [
                        'placeholder' => Yii::t('performance', 'Мерчант')
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'admin_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'view_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'edit_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'share_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'delete_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'switch_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ]) ?>
        </div>
    </div>
</div>
<div class="box-footer">
    <?= Html::saveButton($model) ?>
</div>
<?php ActiveForm::end(); ?>
