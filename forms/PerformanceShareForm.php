<?php

namespace backend\modules\performance\forms;

use common\models\Admin;
use common\models\Performance;
use common\models\PerformanceAdminSettings;
use common\models\query\PerformanceAdminSettingsQuery;
use yii\base\Model;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class PerformanceForm
 * @package backend\modules\performance\forms
 */
class PerformanceShareForm extends Model
{
    public $merchant_id;

    /**
     * @var boolean
     */
    public $admin_enabled;

    /**
     * @var boolean
     */
    public $share_enabled;

    /**
     * @var boolean
     */
    public $edit_enabled;

    /**
     * @var boolean
     */
    public $view_enabled;

    /**
     * @var boolean
     */
    public $switch_enabled;

    /**
     * @var boolean
     */
    public $delete_enabled;

    /**
     * @var Performance
     */
    protected $performance;
    /**
     * @var Admin
     */
    protected $merchant;

    /**
     * @var bool
     */
    protected $isNewRecord;

    /**
     * @return Performance
     */
    public function getPerformance()
    {
        return $this->performance;
    }

    /**
     * @param bool $isNewRecord
     * @return $this
     */
    public function setNewRecord($isNewRecord = true)
    {
        $this->isNewRecord = $isNewRecord;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsNewRecord()
    {
        return $this->isNewRecord;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [
                [
                    'merchant_id',
                    'share_enabled',
                    'switch_enabled',
                    'view_enabled',
                    'delete_enabled',
                    'admin_enabled',
                    'edit_enabled',
                ],
                'required'
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'merchant_id' => Yii::t('models', 'Оператор'),
            'share_enabled' => Yii::t('models', 'Шаринг'),
            'switch_enabled' => Yii::t('models', 'Блокировка'),
            'view_enabled' => Yii::t('models', 'Просмотр'),
            'delete_enabled' => Yii::t('models', 'Удаление'),
            'admin_enabled' => Yii::t('models', 'Управление'),
            'edit_enabled' => Yii::t('models', 'Изменение'),
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
                    $performanceAdminSettingsQuery->shareEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->one()) {
            throw new NotFoundHttpException(Yii::t('errors', 'Неверный идентификатор представления'));
        }
    }

    public function init()
    {
        parent::init();

        if ($this->merchant = Admin::find()->id($this->merchant_id)->one()) {
            if ($performanceAdminSettings = PerformanceAdminSettings::find()
                ->performanceId($this->getId())
                ->merchantId($this->merchant_id)
                ->one()
            ) {
                $this->admin_enabled = $performanceAdminSettings->admin_enabled;
                $this->share_enabled = $performanceAdminSettings->share_enabled;
                $this->edit_enabled = $performanceAdminSettings->edit_enabled;
                $this->delete_enabled = $performanceAdminSettings->delete_enabled;
                $this->view_enabled = $performanceAdminSettings->view_enabled;
                $this->switch_enabled = $performanceAdminSettings->switch_enabled;
            }
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

        $performanceAdminSettings = PerformanceAdminSettings::find()
            ->performanceIdMerchantId(
                $this->getId(),
                $this->merchant_id
            )
            ->andWhere(['<>', 'merchant_id', Yii::$app->getUser()->getId()])
            ->one();

        if (!$performanceAdminSettings) {
            $this->setNewRecord();
            $performanceAdminSettings = $this->performance->newPerformanceAdminSetting([
                'merchant_id' => $this->merchant_id,
            ]);
        }

        $performanceAdminSettings->admin_enabled = $this->admin_enabled;
        $performanceAdminSettings->share_enabled = $this->share_enabled;
        $performanceAdminSettings->edit_enabled = $this->edit_enabled;
        $performanceAdminSettings->delete_enabled = $this->delete_enabled;
        $performanceAdminSettings->view_enabled = $this->view_enabled;
        $performanceAdminSettings->switch_enabled = $this->switch_enabled;
        $performanceAdminSettings->hardSave();

        return true;
    }
}