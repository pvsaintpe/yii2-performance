<?php

use backend\components\View;
use backend\components\Pjax;
use backend\traits\SearchTrait;
use common\components\ActiveRecord;
use pvsaintpe\performance\modules\manager\models\MerchantSearch;
use backend\components\grid\DataColumn;
use yii\helpers\ArrayHelper;
use backend\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Inflector;

/** @var View $this */
/** @var \pvsaintpe\performance\modules\performance\forms\PerformanceSettingsForm $model */
/** @var string $permissionPrefix */

$this->title = Yii::t('payment', 'Настроить представление');
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin([
    'enablePushState' => false,
]);

$searchClass = $model->getPerformance()->search_class;

/** @var SearchTrait|ActiveRecord|MerchantSearch $searchModel */
$searchModel = new $searchClass;

$gridView = Yii::createObject(
    ArrayHelper::merge(
        [
            'dataProvider' => $searchModel->search(),
            'filterModel' => $searchModel,
            'columns' => $searchModel->getGridColumns()
        ],
        [
            'class' => '\backend\widgets\GridView',
        ]
    )
);

$columns = array_intersect(array_keys($searchModel->getGridColumns()), $searchModel->getSafeAttributes());
$disableColumns = array_diff($searchModel->getDisableColumns(), $searchModel->getEnableColumns());
$viewColumns = array_diff($columns, $disableColumns);
$labels = array_intersect_key($searchModel->attributeLabels(), array_flip($columns));

$form = ActiveForm::begin([
    'id' => 'performance-filters',
    'method' => 'GET',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
]);

?>
<table class="table table-bordered table-striped dataTable" role="grid">
<thead>
    <tr role="row">
        <th>Столбец</th>
        <th>Фильтр</th>
        <th>Положение</th>
        <th class="sorting_asc">Сортировка</th>
    </tr>
</thead>

<?php
$i = 1;
foreach ($viewColumns as $attribute) {
    ?>
    <tr role="row" class="<?= ($i % 2 == 0) ? 'odd' : 'even' ?>">
        <?php
        $options = $searchModel->getGridColumns()[$attribute];

        /** @var \backend\components\grid\DataColumn $column */
        $column = Yii::createObject(
            array_merge(
                [
                    'class' => DataColumn::class,
                    'grid' => $gridView,
                ],
                $options
            )
        );
        ?>
        <td class="">
            <?= Select2::widget([
            'attribute' => 'column[]',
            'name' => 'column[]',
            'value' => $attribute,
            'data' => $labels,
            'options' => [
                'placeholder' => Yii::t('layout', 'Столбец'),
                'multiple' => false,
            ],
            'showToggleAll' => true,
            'pluginOptions' => [
                'allowClear' => true,
                'width' => '350px'
            ],
            'pjaxContainerId' => 'w0-pjax',
            ]) ?>
        </td>
        <td class="">
            <?php
                $relations = array_keys($searchModel::singularRelations());
                $relationKey = lcfirst(Inflector::id2camel(str_replace('_id', '', $attribute), '_'));

                if (in_array($attribute, $searchModel::dateAttributes())) {
                    echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
                } elseif (in_array($attribute, $searchModel::datetimeAttributes())) {
                    echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
                } elseif (in_array($attribute, $searchModel::booleanAttributes())) {
                    echo 'boolean';
                } elseif (in_array($relationKey, $relations)) {
                    $selectClass = $searchModel::singularRelations()[$relationKey]['class'];
                    echo Select2::widget([
                        'attribute' => 'filter[]',
                        'name' => 'filter[]',
                        'value' => $attribute,
                        'data' => $selectClass::findForFilter([]),
                        'options' => [
                            'placeholder' => null,
                            'multiple' => true,
                        ],
                        'showToggleAll' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '200px'
                        ],
                        'pjaxContainerId' => 'w0-pjax',
                    ]);
                } else {
                    echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
                }
            ?>
        </td>
        <td class="">
            <input type="text" name="order_position[]" value="<?= $i++ ?>" style="width:100px">
        </td>
        <td align="center">
            <input type="radio" name="sort_strategy[]">
        </td>
    </tr>
    <?php
}
?>
</table>
<?php
ActiveForm::end();
?>
<table class="table table-bordered table-striped dataTable" role="grid">
    <tr role="row">
        <td>
            <?= Select2::widget([
                'attribute' => 'column[]',
                'name' => 'column[]',
                'id' => 'clone-select',
                'value' => null,
                'data' => $labels,
                'options' => [
                    'placeholder' => Yii::t('layout', 'Столбец'),
                    'multiple' => false,
                ],
                'showToggleAll' => true,
                'pluginOptions' => [
                    'allowClear' => true,
                    'width' => '350px',
                ],
                'pjaxContainerId' => 'w0-pjax',
            ]) ?>
        </td>
    </tr>
</table>
<?php
$this->registerJs(<<<JS
    $('#clone-select').change(function() {
            
    });
JS
);

?>
<div id="clone-filters" style="display:none;">
    <?php
        foreach ($columns as $attribute) {
            $relations = array_keys($searchModel::singularRelations());
            $relationKey = lcfirst(Inflector::id2camel(str_replace('_id', '', $attribute), '_'));
            echo '<div id="filter-' . $attribute . '">';
            if (in_array($attribute, $searchModel::dateAttributes())) {
                echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
            } elseif (in_array($attribute, $searchModel::datetimeAttributes())) {
                echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
            } elseif (in_array($attribute, $searchModel::booleanAttributes())) {
                echo 'boolean';
            } elseif (in_array($relationKey, $relations)) {
                $selectClass = $searchModel::singularRelations()[$relationKey]['class'];
                echo Select2::widget([
                    'attribute' => 'filter[]',
                    'name' => 'filter[]',
                    'value' => $attribute,
                    'data' => $selectClass::findForFilter([]),
                    'options' => [
                        'placeholder' => null,
                        'multiple' => true,
                    ],
                    'showToggleAll' => true,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'width' => '200px'
                    ],
                    'pjaxContainerId' => 'w0-pjax',
                ]);
            } else {
                echo '<input class="" type="text" name="filter[]" value="" style="width:200px">';
            }
            echo '</div>';
        }
    ?>
</div>
<?php
$this->registerCss(<<<CSS
.modal-dialog {
    width: 900px!important;
}
CSS
);

Pjax::end();