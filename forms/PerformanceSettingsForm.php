<?php

namespace backend\modules\performance\forms;

use backend\components\grid\DataColumn;
use common\models\Performance;
use common\models\query\PerformanceAdminSettingsQuery;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\base\Model;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class PerformanceForm
 * @package backend\modules\performance\forms
 */
class PerformanceSettingsForm extends Model
{
    /**
     * @var Performance
     */
    protected $performance;

    /**
     * @return Performance
     */
    public function getPerformance()
    {
        return $this->performance;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [];
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
                    $performanceAdminSettingsQuery->adminEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->one()) {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
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

        return true;
    }
}