<?php

namespace pvsaintpe\performance\modules\performance\controllers;

use pvsaintpe\performance\modules\performance\models\PerformanceLanguageSettingsSearch;
use Yii;
use pvsaintpe\performance\models\PerformanceLanguageSettings;
use pvsaintpe\search\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PerformanceLanguageSettingsController implements the CRUD actions for PerformanceLanguageSettings model.
 *
 * @method actionExportConfirm()
 * @method actionExport($fileFormat, $partExport, $params = null)
 */
class PerformanceLanguageSettingsController extends Controller
{
    protected $searchClass = 'pvsaintpe\performance\modules\performance\models\PerformanceLanguageSettingsSearch';

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
                ]
            ]
        ];
    }

    /**
     * Lists all PerformanceLanguageSettings models.
     * @param int|null $performance_id
     * @return mixed
     */
    public function actionIndex($performance_id = null)
    {
        /** @var PerformanceLanguageSettingsSearch $searchModel */
        $searchModel = $this->getSearchModel();
        if ($performance_id) {
            $searchModel->performance_id = $performance_id;
        }

        return $this->renderWithAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(),
        ]);
    }

    /**
     * Updates an existing PerformanceLanguageSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $performance_id
     * @param integer $language_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionUpdate($performance_id, $language_id)
    {
        if (($model = PerformanceLanguageSettings::find()
                        ->pk($performance_id, $language_id)
                        ->one()) === null) {
            return Yii::$app->runAction(
                '/performance/performance-language-settings/index',
                compact('performance_id')
            );
        }

        $request = Yii::$app->request;
        if ($model->load($request->getBodyParams()) && $model->save()) {
            return Yii::$app->runAction(
                '/performance/performance-language-settings/index',
                compact('performance_id')
            );
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the PerformanceLanguageSettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $performance_id
     * @param integer $language_id
     * @return PerformanceLanguageSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($performance_id, $language_id)
    {
        if (($model = PerformanceLanguageSettings::find()
                ->innerJoinWith([
                    'language language',
                    'performance performance'
                ])
                ->pk($performance_id, $language_id)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}
