<?php

namespace backend\modules\performance\models\base;

use Yii;
use common\models\Performance;
use pvsaintpe\search\interfaces\SearchInterface;
use backend\traits\SearchTrait;

/**
 * PerformanceSearchBase represents the model behind the search form about `common\models\Performance`.
 */
class PerformanceSearchBase extends Performance implements SearchInterface
{
    use SearchTrait;

    /**
     * @var bool
     */
    public $default_state;

    /**
     * @var bool
     */
    public $enabled_state;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            [
                [['id', 'merchant_id', 'order_position'], 'integer'],
                [['search_class', 'route', 'name'], 'safe'],
                [['default_state', 'enabled_state'], 'boolean'],
            ]
        );
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'default_state' => Yii::t('models', 'По умолчанию'),
                'enabled_state' => Yii::t('models', 'Вкл.'),
            ]
        );
    }

    /**
     * @return string
     */
    public static function getGridTitle()
    {
        return Yii::t('performance', 'Представления');
    }

    /**
     * @return string
     */
    public function getListTitle()
    {
        return Yii::t('performance', 'Представления') . ': ' . $this->name;
    }

    /**
     * @return array
     */
    public function getListColumns()
    {
        return [
            'id',
            [
                'attribute' => 'merchant_id',
                'value' => function ($form, $widget) {
                    return ($model = $widget->model->merchant) ? $model->getTitleText() : null;
                },
                'format' => 'raw',
            ],
            'search_class',
            'route',
            'name',
            'order_position',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->default_state = $this->isDefault();
        $this->enabled_state = $this->isEnabled();
    }
}
