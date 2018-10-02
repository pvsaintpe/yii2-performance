<?php

namespace backend\modules\performance\forms;

use backend\traits\SearchTrait;
use common\components\ActiveRecord;
use common\models\Performance;
use common\models\PerformanceAdminSettings;
use common\models\PerformanceColumnSettings;
use common\models\query\PerformanceAdminSettingsQuery;
use yii\base\Model;
use Yii;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

/**
 * Class PerformanceForm
 * @package backend\modules\performance\forms
 */
class PerformanceForm extends Model
{
    public $name;
    public $search_class;
    public $url_params;
    public $query_params;
    public $instance_performance_id;
    public $route;
    public $is_default = 0;
    public $order_position;
    public $enabled = 1;
    public $system_defined = 0;

    /**
     * @var Performance
     */
    protected $performance;

    /**
     * @var bool
     */
    protected $isNewRecord = false;

    /**
     * @return $this
     */
    protected function setNewRecord()
    {
        $this->isNewRecord = true;
        return $this;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'search_class', 'route'], 'required'],
            [
                [
                    'is_default',
                    'order_position',
                    'enabled',
                    'system_defined',
                    'id',
                    'url_params',
                    'query_params',
                    'instance_performance_id',
                ],
                'safe'
            ],
            [['name'], 'validateName'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'merchant_id' => Yii::t('models', 'Мерчант'),
            'search_class' => Yii::t('models', 'Search-модель'),
            'route' => Yii::t('models', 'URL грида'),
            'name' => Yii::t('models', 'Название'),
            'order_position' => Yii::t('models', 'Порядок'),
            'is_default' => Yii::t('models', 'По умолчанию'),
            'enabled' => Yii::t('models', 'Вкл.'),
            'system_defined' => Yii::t('models', 'Системное'),
        ];
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function setId($id)
    {
        if (!$this->performance = Performance::find()
            ->id($id)
            ->innerJoinWith([
                'performanceAdminSettings' => function(PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->editEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->one()) {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }

        $this->setAttributes($this->performance->attributes);
    }

    /**
     * @param $attribute
     * @param $options
     */
    public function validateName($attribute, $options)
    {
        if ($exists = Performance::find()
            ->merchantId(Yii::$app->getUser()->getId())
            ->searchClass($this->search_class)
            ->route($this->route)
            ->name($this->{$attribute})
            ->andFilterWhere(['<>', 'id', $this->getId()])
            ->one()) {
            $this->addError($attribute, Yii::t('error', 'Название уже используется в другом представлении.'));
        }
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->performance ? $this->performance->id : null;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $instanceExists = Performance::find()->id($this->instance_performance_id)->one();

        if (!$this->performance) {
            $this->setNewRecord();
            $this->performance = new Performance([
                'merchant_id' => Yii::$app->getUser()->getId(),
                'search_class' => $this->search_class,
                'route' => $this->route,
                'enabled' => $this->enabled,
                'url_params' => $this->url_params,
                'query_params' => $this->query_params,
                'instance_performance_id' => $instanceExists ? $this->instance_performance_id : null,
            ]);
        }

        $this->performance->enabled = $this->enabled;
        $this->performance->system_defined = $this->system_defined;
        $this->performance->order_position = $this->order_position;
        $this->performance->name = $this->name;
        $this->performance->hardSave();

        if ($this->isNewRecord) {
            if (!$instanceExists) {
                $searchClass = $this->performance->search_class;
                /** @var SearchTrait|ActiveRecord $searchModel */
                $searchModel = new $searchClass;
                $columns = array_keys($searchModel->getGridColumns());
                $disableColumns = array_diff($searchModel->getDisableColumns(), $searchModel->getEnableColumns());
                $viewColumns = array_diff($columns, $disableColumns);
                $i = 1;
                foreach ($viewColumns as $attribute) {
                    $column = $this->performance->newPerformanceColumnSetting();
                    $column->attribute = $attribute;
                    $column->order_position = 10 * $i++;
                    $column->value = null;
                    $column->hardSave();
                }
            }

            $queryParams = $this->performance->query_params ? unserialize($this->performance->query_params) : [];
            $columnSettings = PerformanceColumnSettings::find()
                ->performanceId($this->performance->id)
                ->all();
            foreach ($columnSettings as $columnSetting) {
                if (isset($queryParams[$columnSetting->attribute])) {
                    $values = $columnSetting->value;
                    switch ($columnSetting->type) {
                        case 'select':
                            $columnSetting->value = array_merge(
                                is_null($values) ? [] : $values,
                                (array)$queryParams[$columnSetting->attribute]
                            );
                            break;
                        default:
                            $columnSetting->value = $queryParams[$columnSetting->attribute];
                    }
                    $columnSetting->value = serialize($columnSetting->value);
                    $columnSetting->save();
                }
            }
        }

        if ($this->is_default) {
            $command = Yii::$app->db->createCommand(
                "
    UPDATE `performance_admin_settings`, `performance`
    SET `performance_admin_settings`.`is_default` = 0
    WHERE `performance`.`route` = :route
    AND `performance`.`search_class` = :search_class
    AND `performance_admin_settings`.`merchant_id` = :merchant_id
            ",
                [
                    'route' => $this->performance->route,
                    'search_class' => $this->performance->search_class,
                    'merchant_id' => Yii::$app->getUser()->getId(),
                ]
            );
            $command->execute();

            $model = PerformanceAdminSettings::find()
                ->performanceId($this->performance->id)
                ->merchantId(Yii::$app->getUser()->getId())
                ->one();

            $model->is_default = 1;
            $model->save(true, ['is_default']);
        }

        return true;
    }
}
