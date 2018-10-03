<?php

namespace pvsaintpe\performance\modules\performance\controllers;

use Yii;
use pvsaintpe\performance\models\PerformanceColumnSettings;
use pvsaintpe\performance\modules\performance\models\PerformanceColumnSettingsSearch;
use pvsaintpe\search\components\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * PerformanceColumnSettingsController implements the CRUD actions for PerformanceColumnSettings model.
 *
 * @method actionExportConfirm()
 */
class PerformanceColumnSettingsController extends Controller
{
    protected $searchClass = 'pvsaintpe\performance\modules\performance\models\PerformanceColumnSettingsSearch';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'create' => ['get', 'post'],
                    'delete' => ['post'],
                ]
            ]
        ];
    }

    /**
     * Lists all PerformanceColumnSettings models.
     * @param int|null $performance_id
     * @param bool $reload
     * @return mixed
     */
    public function actionIndex($performance_id = null, $reload = false)
    {
        /** @var PerformanceColumnSettingsSearch $searchModel */
        $searchModel = $this->getSearchModel();
        if ($performance_id) {
            $searchModel->performance_id = $performance_id;
        }

        return $this->renderWithAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(),
            'reload' => $reload,
        ]);
    }

    /**
     * Delete an existing Performance Column Settings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $performance_id
     * @param string $attribute
     * @return mixed
     * @throws \Throwable
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($performance_id, $attribute)
    {
        $reload = true;
        if (($model = PerformanceColumnSettings::find()
                ->innerJoinWith(['performance performance'])
                ->pk($performance_id, $attribute)
                ->one()) !== null) {
            $model->delete();
        }

        return Yii::$app->runAction(
            '/performance/performance-column-settings/index',
            compact('performance_id', 'reload')
        );
    }

    /**
     * Create an existing PerformanceColumnSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionCreate($id)
    {
        $reload = true;
        $request = Yii::$app->request;
        $model = new PerformanceColumnSettings([
            'performance_id' => $id,
        ]);

        if ($model->load($request->getBodyParams()) && $model->save()) {
            return Yii::$app->runAction(
                '/performance/performance-column-settings/index',
                compact('performance_id', 'reload')
            );
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PerformanceColumnSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $performance_id
     * @param string $attribute
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionUpdate($performance_id, $attribute)
    {
        $reload = true;
        $request = Yii::$app->request;
        if (($model = PerformanceColumnSettings::find()
                ->pk($performance_id, $attribute)
                ->one()) === null) {
            return Yii::$app->runAction(
                '/performance/performance-column-settings/index',
                compact('performance_id', 'reload')
            );
        }

        if ($model->load($request->getBodyParams()) && $model->save()) {
            return Yii::$app->runAction(
                '/performance/performance-column-settings/index',
                compact('performance_id', 'reload')
            );
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the PerformanceColumnSettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $performance_id
     * @param string $attribute
     * @return PerformanceColumnSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($performance_id, $attribute)
    {
        if (($model = PerformanceColumnSettings::find()
                ->innerJoinWith([
                    'performance performance'
                ])
                ->pk($performance_id, $attribute)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}
