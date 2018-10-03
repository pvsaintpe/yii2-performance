<?php

namespace pvsaintpe\performance\traits;

use pvsaintpe\performance\interfaces\PerformanceInterface;
use pvsaintpe\search\components\View;
use pvsaintpe\search\helpers\Html;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Application;

/**
 * Trait SearchTrait
 * @package backend\traits
 */
trait SearchTrait
{
    protected $disableColumns = [];
    protected $enableColumns = [];
    protected $separateBySheets = false;
    protected $showTitle = true;
    protected $showTableTitle = true;
    protected $showPageSummary = true;

    protected $searchFilters = [];

    /**
     * Запрещенные действия
     * @var array
     */
    protected $restrictActions = [];

    /**
     * @return string
     */
    public function formName()
    {
        return static::getFormName();
    }

    /**
     * @return string
     */
    public static function getFormName()
    {
        return 't';
    }

    /**
     * @var null
     */
    protected $page = null;

    /**
     * @param array $restrictActions
     */
    public function setRestrictActions($restrictActions = [])
    {
        $this->restrictActions = $restrictActions;
    }

    /**
     * @return array
     */
    public function getRestrictActions()
    {
        return $this->restrictActions;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setSearchFilters($filters = [])
    {
        $this->searchFilters = $filters;
        return $this;
    }

    /**
     * @return array
     */
    public function getSearchFilters()
    {
        return $this->searchFilters;
    }

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $updatePath;

    /**
     * @var View
     */
    private $_view;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $permissionPrefix;

    /**
     * @var null|array
     */
    protected $defaultOrder;

    /**
     * @return bool
     */
    public function getSeparateBySheets()
    {
        return $this->separateBySheets;
    }

    /**
     * @return string
     */
    public function getSheetTitle()
    {
        return Yii::t('export', 'Лист');
    }

    /**
     * @return bool
     */
    public function getShowTitle()
    {
        return $this->showTitle;
    }

    /**
     * @return bool
     */
    public function getShowTableTitle()
    {
        return $this->showTableTitle;
    }

    /**
     * @return bool
     */
    public function getShowPageSummary()
    {
        return $this->showPageSummary;
    }

    /**
     * @return array
     */
    public function getSafeAttributes()
    {
        $attributes = array_flip(array_keys($this->attributes));
        foreach ($this->rules() as $rule) {
            if (is_array($rule[0])) {
                $attributes = array_merge($attributes, array_flip($rule[0]));
            } else {
                $attributes = array_merge($attributes, [$rule[0] => $this->{$rule[0]}]);
            }
        }
        return array_keys($attributes);
    }

    /**
     * @return array
     */
    public function getAfterSummaryColumns()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getDisableColumns()
    {
        return $this->disableColumns;
    }

    /**
     * @return mixed
     */
    public function getEnableColumns()
    {
        return $this->enableColumns;
    }

    /**
     * @return array
     */
    public function getGridColumns()
    {
        return [];
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getFilter($attribute)
    {
        $filters = static::getFilters();

        if (isset($filters[$attribute])) {
            return $filters[$attribute];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return $this
     */
    public function setFilters($filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @var int
     */
    protected $paginationSize = 20;

    /**
     * @var \pvsaintpe\search\components\ActiveQuery
     */
    protected $query;

    public function getPageAttributes()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return [
            'pageSize' => $this->getPaginationSize(),
            'pageSizeLimit' => $this->getPaginationSize(),
        ];
    }

    /**
     * @return int
     */
    public function getPaginationSize()
    {
        return $this->paginationSize;
    }

    /**
     * @return array
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @var array
     */
    protected $sort = [];

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return void
     */
    public function modifyQuery()
    {
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails */
            // $this->query->where('0=1');
        }
    }

    /**
     * @return ActiveDataProvider
     */
    public function getDataProvider()
    {
        $this->modifyQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => $this->getPagination(),
        ]);

        if (($modifySort = static::getSort()) !== false) {
            $sort = $dataProvider->getSort();

            foreach ($modifySort as $attribute => $options) {
                if (property_exists($sort, $attribute)) {
                    $sort->{$attribute} = ArrayHelper::merge(
                        $sort->{$attribute},
                        $options
                    );
                }
            }

            if ($this->defaultOrder) {
                $sort->defaultOrder = $this->defaultOrder;
            }

            $dataProvider->setSort($sort);
        } else {
            $dataProvider->setSort(false);
        }

        return $dataProvider;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setDefaultOrder($order = null)
    {
        if ($order) {
            if (preg_match('/^([-]?)(.*)/', $order, $matches)) {
                list(, $direction, $column) = $matches;
                $this->defaultOrder = [
                    $column => $direction ? SORT_DESC : SORT_ASC,
                ];
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getGridToolbar()
    {
        $buttons = [
            $this->getGridReset(),
            $this->getGridExport()
        ];

        if ($this instanceof PerformanceInterface) {
            return array_merge($this->getGridPerformanceToolbar(), $buttons);
        }

        return $buttons;
    }

    /**
     * @return array
     */
    public function getGridReset()
    {
        $url = ['index'];
        foreach ($this->getPageAttributes() as $attribute) {
            if (!empty($this->{$attribute})) {
                $key = $this->formName() . "[$attribute]";
                $url[$key] = $this->{$attribute};
            }
        }
        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-repeat"></i>',
                $url,
                [
                    'data-pjax' => 0,
                    'class' => 'btn btn-default btn-md',
                    'title' => Yii::t('info', 'Сбросить')
                ]
            ),
        ];
    }

    /**
     * @param null|integer $id
     * @param string $class
     * @return array
     */
    public function getGridExport($id = null, $class = 'btn-md')
    {
        return [
            'content' => Html::a(
                '<i class="glyphicon glyphicon-export"></i>',
                [
                    str_replace('/view/', '/export-confirm', $this->viewPath),
                    'page' => Yii::$app->request->get('page'),
                    'params' => serialize(array_merge(Yii::$app->request->queryParams, $id ? ['id' => $id] : [])),
                ],
                [
                    'data-orig-url' => Url::toRoute([str_replace('/view/', '/export-confirm', $this->viewPath),
                        'page' => Yii::$app->request->get('page'),
                        'params' => serialize(Yii::$app->request->queryParams)]),
                    'data-namespace' => $this->getShortName(),
                    'data-params' => '',
                    'class' => "btn btn-success $class btn-main-modal",
                    'title' => Yii::t('export', 'Экспорт в файл'),
                    'id' => 'export'
                ]
            ),
        ];
    }

    /**
     * @param $permissionPrefix
     */
    public function setPermissionPrefix($permissionPrefix)
    {
        $this->permissionPrefix = $permissionPrefix;
    }

    /**
     * @return mixed
     */
    public function getPermissionPrefix()
    {
        return $this->permissionPrefix;
    }

    /**
     * @param View|\yii\base\View $view
     */
    public function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * @param $viewPath
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return mixed
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * @param $updatePath
     */
    public function setUpdatePath($updatePath)
    {
        $this->updatePath = $updatePath;
    }

    /**
     * @return mixed
     */
    public function getUpdatePath()
    {
        return $this->updatePath;
    }

    /**
     * @var
     */
    protected $disableAttributes = [];

    /**
     * @var
     */
    protected $enableAttributes = [];

    /**
     * @return mixed
     */
    public function getDisableAttributes()
    {
        return $this->disableAttributes;
    }

    /**
     * @return mixed
     */
    public function getEnableAttributes()
    {
        return $this->enableAttributes;
    }

    /**
     * @return \pvsaintpe\search\components\ActiveQuery
     */
    public function getRawQuery()
    {
        return $this->query;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        if (is_null($this->page) && Yii::$app instanceof Application) {
            $page = Yii::$app->getRequest()->get('page', null);
            if (!is_null($page)) {
                $this->page = $page - 1;
            }
        }

        return $this->page;
    }

    /**
     * @param null|integer $page
     * @return $this
     */
    public function setPage($page = null)
    {
        if (!is_null($page)) {
            $this->page = $page;
        }

        return $this;
    }
}
