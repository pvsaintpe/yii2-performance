<?php

namespace pvsaintpe\performance\modules\performance\controllers;

use pvsaintpe\performance\modules\performance\forms\PerformanceForm;
use pvsaintpe\search\widgets\ActiveForm;
use pvsaintpe\performance\models\PerformanceAdminSettings;
use pvsaintpe\performance\models\PerformanceColumnSettings;
use pvsaintpe\performance\models\PerformanceLanguageSettings;
use pvsaintpe\performance\models\query\PerformanceAdminSettingsQuery;
use Yii;
use pvsaintpe\performance\models\Performance;
use pvsaintpe\search\components\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use pvsaintpe\search\components\ActiveRecord;
use yii\web\Response;

/**
 * DefaultController implements the CRUD actions for Performance model.
 *
 * @method actionExportConfirm()
 */
class DefaultController extends Controller
{
    protected $searchClass = 'pvsaintpe\performance\modules\performance\models\PerformanceSearch';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update' => ['get', 'post'],
                    'create' => ['get', 'post'],
                    'delete' => ['get'],
                ]
            ]
        ];
    }

    /**
     * Lists all Performance models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var ActiveRecord $searchModel */
        $searchModel = $this->getSearchModel();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(),
        ]);
    }

    /**
     * Create a new Performance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $search_class
     * @param string $route
     * @param string $url_params
     * @param string $query_params
     * @param string $instance_performance_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate(
        $search_class,
        $route,
        $url_params = null,
        $query_params = null,
        $instance_performance_id = null
    ) {
        $model = new PerformanceForm([
            'search_class' => $search_class,
            'route' => $route,
            'url_params' => $url_params,
            'query_params' => $query_params,
            'instance_performance_id' => $instance_performance_id,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            // ajax validation
            if (Yii::$app->getRequest()->getIsAjax() && (Yii::$app->getRequest()->getBodyParam('ajax') == $model->formName())) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                $url_parts = parse_url(Yii::$app->getRequest()->getReferrer());
                $params = [];
                if (isset($url_parts['query'])) {
                    parse_str($url_parts['query'], $params);
                }
                if (isset($params['t']['performance_id'])) {
                    unset($params['t']['performance_id']);
                }
                $params = array_merge(
                    $params,
                    [
                        't' => [
                            'performance_id' => $model->getId()
                        ]
                    ]
                );
                $url_parts['query'] = http_build_query($params);
                return $this->redirect($url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query']);
            }
        }

        return $this->renderWithAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Performance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $model = new PerformanceForm([
            'id' => $id,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            // ajax validation
            if (Yii::$app->getRequest()->getIsAjax() && (Yii::$app->getRequest()->getBodyParam('ajax') == $model->formName())) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }
        }

        return $this->renderWithAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete an existing Performance model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @throws NotFoundHttpException if the model cannot be found
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!$model = Performance::find()
            ->id($id)
            ->innerJoinWith([
                'performanceAdminSettings' => function(PerformanceAdminSettingsQuery $performanceAdminSettingsQuery) {
                    $performanceAdminSettingsQuery->deleteEnabled(1);
                    $performanceAdminSettingsQuery->merchantId(Yii::$app->getUser()->getId());
                }
            ])
            ->one()) {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }

        PerformanceLanguageSettings::deleteAll('performance_id = :id', [
            'id' => $id,
        ]);

        PerformanceColumnSettings::deleteAll('performance_id = :id', [
            'id' => $id,
        ]);

        PerformanceAdminSettings::deleteAll('performance_id = :id', [
            'id' => $id,
        ]);

        Performance::updateAll(
            ['instance_performance_id' => null],
            ['instance_performance_id' => $id]
        );

        Performance::deleteAll('id = :id', [
            'id' => $id,
        ]);

        $url_parts = parse_url(Yii::$app->getRequest()->getReferrer());
        $params = [];
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $params);
        }
        if (isset($params['t']['performance_id'])) {
            unset($params['t']['performance_id']);
            if (isset($params['t']) && count($params['t']) < 1) {
                unset($params['t']);
            }
        }
        $url_parts['query'] = http_build_query($params);
        return $this->redirect($url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query']);
    }

    /**
     * Finds the Performance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Performance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Performance::find()
                ->innerJoinWith([
                    'merchant merchant'
                ])
                ->pk($id)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}
