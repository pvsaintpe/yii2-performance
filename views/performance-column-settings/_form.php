<?php

use backend\helpers\Html;
use pvsaintpe\log\widgets\ActiveForm;
use common\models\Performance;
use backend\traits\SearchTrait;
use common\components\ActiveRecord;
use common\models\PerformanceColumnSettings;

/* @var $this yii\web\View */
/* @var $model common\models\PerformanceColumnSettings */
/* @var $form pvsaintpe\log\widgets\ActiveForm */
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
<div class="performance-column-settings-form">
    <?php
        if ($model->getIsNewRecord()) {
        $searchClass = $model->performance->search_class;
        /** @var SearchTrait|ActiveRecord $searchModel */
        $searchModel = new $searchClass();
        $attributes = [];
        foreach ($searchModel->getGridColumns() as $attribute => $column) {
            if (PerformanceColumnSettings::find()
                ->performanceId($model->performance_id)
                ->attribute($attribute)
                ->count() > 0
            ) {
                continue;
            }
            $attributes[$attribute] = $searchModel->getAttributeLabel($attribute);
        }
    ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'attribute')->dropDownList($attributes) ?>
        </div>
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-md-6">
            <?php
                switch ($model->type) {
                    case 'string':
                    case 'decimal':
                        ?>
                        <?= $form->field($model, 'value')->textInput([
                            'maxlength' => true,
                        ]) ?>
                        <?php
                        break;
                    case 'boolean':
                        ?>
                        <?= $form->field($model, 'value')->dropDownList([
                            null => Yii::t('backend', 'Не задано'),
                            0 => Yii::t('backend', 'Нет'),
                            1 => Yii::t('backend', 'Да')
                        ]) ?>
                        <?php
                        break;
                    case 'select':
                        /** @var ActiveRecord $className */
                        $className = $model->relation_class;
                        $searchClass = $model->performance->search_class;
                        /** @var ActiveRecord $searchModel */
                        $searchModel = new $searchClass();
                        ?>
                        <?= $form->field($model, 'value')->widget(\kartik\widgets\Select2::class, [
                            'data' => $className::findForFilter([]),
                            'options' => [
                                'placeholder' => $searchModel->getAttributeLabel($model->attribute),
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]) ?>
                        <?php
                        break;
                    case 'integer':
                        ?>
                        <?= $form->field($model, 'value')->widget(\kartik\widgets\TouchSpin::class, [
                            'pluginOptions' => [
                                'verticalbuttons' => true,
                                'min' => 0,
                                'max' => 99999,
                            ]
                        ]); ?>
                        <?php
                        break;
                    default:
                        ?>
                        <?= $form->field($model, 'value')->textInput([
                            'maxlength' => true,
                            'disabled' => true,
                        ]) ?>
                        <?php
                }
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'values_hidden')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно')
            ], ['disabled' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'order_position')->widget(\kartik\widgets\TouchSpin::class, [
                'pluginOptions' => [
                    'verticalbuttons' => true,
                    'min' => 0,
                    'max' => 99999,
                ]
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sort_strategy')->dropDownList([
                null => Yii::t('backend', 'Не задано'),
                SORT_ASC => Yii::t('backend', 'По возрастанию'),
                SORT_DESC => Yii::t('backend', 'По убыванию')
            ]) ?>
        </div>
    </div>
    <?php
    /*
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'protected')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно'),
            ], ['disabled' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'required')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно'),
            ], ['disabled' => true]) ?>
        </div>
    </div>
    */
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'hidden')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно'),
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'export_enabled')->dropDownList([
                0 => Yii::t('backend', 'Неактивно'),
                1 => Yii::t('backend', 'Активно'),
            ]) ?>
        </div>
    </div>
</div>
<div class="box-footer">
    <?= Html::saveButton($model) ?>
</div>
<?php ActiveForm::end(); ?>
