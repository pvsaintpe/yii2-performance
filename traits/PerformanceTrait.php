<?php

namespace pvsaintpe\performance\traits;

use pvsaintpe\search\helpers\Html;
use pvsaintpe\performance\helpers\Serializer;
use pvsaintpe\performance\modules\performance\models\PerformanceAdminSettingsSearch;
use pvsaintpe\performance\modules\performance\models\PerformanceColumnSettingsSearch;
use pvsaintpe\performance\modules\performance\models\PerformanceLanguageSettingsSearch;
use pvsaintpe\performance\modules\performance\models\PerformanceSearch;
use pvsaintpe\performance\models\Performance;
use pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery;
use Yii;
use yii\helpers\Url;

/**
 * Trait PerformanceTrait
 * @package backend\modules\performance\traits
 */
trait PerformanceTrait
{
    /**
     * @var int|null
     */
    public $performance_id = null;

    /**
     * @return array
     */
    public function requiredAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), $this->performanceRules());
    }

    public function initPerformance()
    {
        if (Yii::$app->id == 'app-backend' && !$this->performance_id && Yii::$app->user->can('role_merchant')) {
            /** @var Performance $performance */
            $performanceQuery = Performance::find();
            if ($performance = $performanceQuery
                ->andWhere($performanceQuery->a($this->getPerformanceFilters()))
                ->innerJoinWith([
                    'performanceAdminSettings pas' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                        $performanceAdminSettingsQuery->merchantId(Yii::$app->user->getId());
                        $performanceAdminSettingsQuery->isDefault();
                        $performanceAdminSettingsQuery->enabled();
                    }
                ])
                ->one()
            ) {
                if ($performance->allowedAdmin() || $performance->allowedView()) {
                    $this->performance_id = $performance->id;
                    Yii::$app->request->setQueryParams(array_merge(Yii::$app->request->getQueryParams(), [
                        PerformanceSearch::getFormName() => ['performance_id' => $this->performance_id],
                    ]));
                }
            }
        }
    }

    public function initPerformanceFilters()
    {
        if (!$this->performance_id) {
            $this->initPerformance();
        }

        if ($this->performance_id && $performance = Performance::find()->id($this->performance_id)->one()) {
            if ($columnSettings = $performance->getPerformanceColumnSettings()->andWhere([
                'NOT', ['value' => null]
            ])->all()) {
                foreach ($columnSettings as $columnSetting) {
                    $defaultValue = $columnSetting->value;
                    if ($columnSetting->type == 'select'
                        && $columnSetting->value
                        && Serializer::isSerialized($columnSetting->value)
                    ) {
                        $defaultValue = unserialize($columnSetting->value);
                    }
                    $this->query->andWhere([
                        $this->query->a($columnSetting->attribute) => $defaultValue
                    ]);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPerformanceGridTitle()
    {
        if (!$this->performance_id) {
            $this->initPerformance();
        }

        if ($this->performance_id && $performance = Performance::find()->id($this->performance_id)->one()) {
            if ($template = $performance->getPerformanceLanguageSettings()
                ->languageId(Language::getIdByCode(Yii::$app->language))
                ->one()
            ) {
                return $template->name;
            }
        }
        return static::getGridTitle();
    }

    /**
     * @return array
     */
    public function getPerformanceGridColumns()
    {
        if ($this->performance_id && $performance = Performance::find()->id($this->performance_id)->one()) {
            $performanceColumns = $performance->getPerformanceColumnSettings()
                ->alias('pcs')
                ->select('pcs.attribute')
                ->hidden(0)
                ->orderBy([
                    'pcs.order_position' => SORT_ASC
                ])
                ->column();
            $filteredColumns = array_intersect_key(
                $this->getGridColumns(),
                array_flip($performanceColumns)
            );
            $columns = [];
            foreach ($performanceColumns as $attribute) {
                $columns[$attribute] = $filteredColumns[$attribute];
            }
            return $columns;
        } else {
            $columns = array_diff_key(
                $this->getGridColumns(),
                array_flip($this->getDisableColumns())
            );
        }
        return $columns;
    }

    /**
     * @return array
     */
    public function performanceRules()
    {
        if (Yii::$app->id == 'app-backend' && Yii::$app->user->can('role_merchant')) {
            return [
                [['performance_id'], 'integer'],
            ];
        } else {
            return [];
        }
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformance($searchClass = null, $route = null)
    {
        return [
            'content' => $this->getView()->render('@app/modules/performance/widgets/dropdown.php', [
                'search_class' => $searchClass ?: static::class,
                'route' => $route ?: Yii::$app->urlManager->parseRequest(Yii::$app->request)[0],
                'model' => $this,
            ]),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceOptions($searchClass = null, $route = null)
    {
        $url = ['/performance/performance-column-settings/index'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->adminEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url[PerformanceColumnSettingsSearch::getFormName() . '[performance_id]'] = $performance->id;

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-cog"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'data-dismiss' => 'modal',
                    'class' => 'btn btn-warning btn-md btn-main-modal',
                    'title' => Yii::t('info', 'Настройка представления')
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceDelete($searchClass = null, $route = null)
    {
        $url = ['/performance/default/delete'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->deleteEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url['id'] = $performance->id;

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-remove"></i>',
                $url,
                [
                    'class' => 'btn btn-danger btn-md',
                    'title' => Yii::t('info', 'Удаление представления')
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceDefault($searchClass = null, $route = null)
    {
        $url = ['/performance/performance-admin-settings/default'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->viewEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url['id'] = $performance->id;
        $url[PerformanceSearch::getFormName() . '[performance_id]'] = $performance->id;

        $performanceAdminSettings = $performance->getPerformanceAdminSettings()
            ->merchantId(Yii::$app->user->getId())
            ->one();

        $emptyFlag = $performanceAdminSettings->is_default ? '' : '-empty';

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-heart' . $emptyFlag . '"></i>',
                $url,
                [
                    'class' => 'btn btn-info btn-md',
                    'title' => Yii::t('models', 'По умолчанию'),
                    'data-method' => "POST"
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceUpdate($searchClass = null, $route = null)
    {
        $url = ['/performance/default/update'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->editEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url['id'] = $performance->id;

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-pencil"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'data-dismiss' => 'modal',
                    'class' => 'btn btn-primary btn-md btn-main-modal',
                    'title' => Yii::t('info', 'Редактирование представления')
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceShare($searchClass = null, $route = null)
    {
        $url = ['/performance/performance-admin-settings/index'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->shareEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url[PerformanceAdminSettingsSearch::getFormName() . '[performance_id]'] = $performance->id;

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-share-alt"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'data-dismiss' => 'modal',
                    'class' => 'btn btn-default btn-md btn-main-modal',
                    'title' => Yii::t('info', 'Поделиться представлением')
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceLanguageSettings($searchClass = null, $route = null)
    {
        $url = ['/performance/performance-language-settings/index'];

        if (!$performance = Performance::find()
            ->alias('performance')
            ->innerJoinWith([
                'performanceAdminSettings' => function (PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->editEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->id($this->performance_id)
            ->one()) {
            return null;
        }

        $url[PerformanceLanguageSettingsSearch::getFormName() . '[performance_id]'] = $performance->id;

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-globe"></i>',
                $url,
                [
                    'data-pjax' => true,
                    'data-method' => 'POST',
                    //'class' => 'btn-main-modal',
                    //'data-dismiss' => 'modal',
                    'class' => 'btn btn-default btn-md btn-main-modal',
                    'title' => Yii::t('info', 'Локализация представления')
                ]
            ),
        ];
    }

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceCreate($searchClass = null, $route = null)
    {
        $url = ['/performance/default/create'];
        $url['search_class'] = $searchClass ?: static::class;
        $url['route'] = $route ?: Yii::$app->urlManager->parseRequest(Yii::$app->request)[0];
        $urlParams = [];
        foreach ($this->getPageAttributes() as $attribute) {
            $urlParams[$attribute] = $this->{$attribute};
        }
        $url['url_params'] = serialize($urlParams);
        $url['instance_performance_id'] = $this->performance_id > 0 ? $this->performance_id : null;
        $queryParams = array_diff_key(
            array_merge(
                array_filter($this->getAttributes()),
                array_filter(Yii::$app->getRequest()->get('t') ?? [])
            ),
            array_flip(['performance_id'])
        );
        $url['query_params'] = serialize($queryParams);

        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-plus"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'data-orig-url' => Url::toRoute($url),
                    'data-namespace' => $this->getShortName(),
                    'data-params' => '',
                    'data-method' => 'POST',
                    'class' => 'btn btn-success btn-md btn-main-modal',
                    'title' => Yii::t('info', 'Создать представление')
                ]
            ),
        ];
    }

    /**
     * @return array
     */
    public function getPerformanceFilters()
    {
        return [
            'route' => Yii::$app->urlManager->parseRequest(Yii::$app->request)[0],
            'search_class' => static::class,
            'enabled' => 1,
        ];
    }

    /**
     * @return array
     */
    public function getGridPerformanceToolbar()
    {
        if (Yii::$app->id != 'app-backend') {
            return [];
        }

        if (!Yii::$app->user->can('role_merchant')) {
            return [];
        }

        $buttons = [
            $this->getGridPerformance(),
            $this->getGridPerformanceCreate(),
        ];

        if (!empty($this->performance_id)) {
            $buttons = array_merge($buttons, [
                $this->getGridPerformanceUpdate(),
                $this->getGridPerformanceDelete(),
                $this->getGridPerformanceDefault(),
                $this->getGridPerformanceShare(),
                $this->getGridPerformanceLanguageSettings(),
                $this->getGridPerformanceOptions(),
            ]);
        }

        return array_filter($buttons);
    }
}
