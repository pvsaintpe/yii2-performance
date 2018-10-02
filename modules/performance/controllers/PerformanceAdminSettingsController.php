<?php

namespace backend\modules\performance\controllers;

use backend\modules\performance\forms\PerformanceShareForm;
use backend\widgets\ActiveForm;
use common\models\Performance;
use Yii;
use common\models\PerformanceAdminSettings;
use backend\modules\performance\models\PerformanceAdminSettingsSearch;
use backend\components\Controller;
use yii\boost\base\InvalidModelException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\ActiveRecord;
use yii\web\Response;

/**
 * PerformanceAdminSettingsController implements the CRUD actions for PerformanceAdminSettings model.
 *
 * @method actionExportConfirm()
 */
class PerformanceAdminSettingsController extends Controller
{
    protected $searchClass = 'backend\modules\performance\models\PerformanceAdminSettingsSearch';

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
     * Lists all PerformanceAdminSettings models.
     * @param int|null $performance_id
     * @return mixed
     */
    public function actionIndex($performance_id = null)
    {
        /** @var PerformanceAdminSettingsSearch $searchModel */
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
     * Updates an existing PerformanceAdminSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $model = new PerformanceShareForm([
            'id' => $id,
        ]);
        $model->setNewRecord(true);

        if ($model->load($request->getBodyParams()) && $model->save()) {
            return Yii::$app->runAction(
                '/performance/performance-admin-settings/index',
                [
                    'performance_id' => $id,
                ]
            );
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PerformanceAdminSettings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $performance_id
     * @param integer $merchant_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionUpdate($performance_id, $merchant_id)
    {
        $model = new PerformanceShareForm([
            'id' => $performance_id,
            'merchant_id' => $merchant_id,
        ]);
        $model->setNewRecord(false);

        $request = Yii::$app->request;
        if ($model->load($request->getBodyParams()) && $model->save()) {
            return Yii::$app->runAction(
                '/performance/performance-admin-settings/index',
                compact('performance_id')
            );
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete an existing Performance Admin Settings model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $performance_id
     * @param integer $merchant_id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($performance_id, $merchant_id)
    {
        if (($model = PerformanceAdminSettings::find()
                ->innerJoinWith([
                    'performance performance'
                ])
                ->pk($performance_id, $merchant_id)
                ->one()) !== null) {
            $model->delete();
        }

        return Yii::$app->runAction(
            '/performance/performance-admin-settings/index',
            compact('performance_id')
        );
    }

    /**
     * Включение/выключение.
     * @param integer $id
     * @return mixed
     * @throws InvalidModelException
     */
    public function actionSwitch($id)
    {
        if (!method_exists($this, 'findModel')) {
            $this->redirect('index');
        }

        $model = $this->findModel($id, Yii::$app->getUser()->getId());

        $model->enabled = !$model->enabled;
        $model->save(false);

        $request = Yii::$app->getRequest();
        if ($request->getIsPjax()) {
            return $this->runReferrerRequest(['t' => [
                'performance_id' => $id,
            ]]);
        } else {
            if ($request->getReferrer()) {
                $url_parts = parse_url($request->getReferrer());
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
                            'performance_id' => $id
                        ]
                    ]
                );
                $url_parts['query'] = http_build_query($params);
                return $this->redirect($url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query']);
            }

            return $this->redirect(['index', 't[performance_id]' => $id]);
        }
    }

    /**
     * Установка значения по умолчанию.
     * @param integer $id
     * @return mixed
     * @throws InvalidModelException
     */
    public function actionDefault($id)
    {
        $model = $this->findModel($id, Yii::$app->getUser()->getId());

        if (!$model->is_default) {
            $command = Yii::$app->db->createCommand(
                "
    UPDATE `performance_admin_settings`
    INNER JOIN `performance` ON `performance`.`id` = `performance_admin_settings`.`performance_id`
      AND `performance`.`route` = :route
      AND `performance`.`search_class` = :search_class
    SET `performance_admin_settings`.`is_default` = 0
    WHERE `performance_admin_settings`.`merchant_id` = :merchant_id
            ",
                [
                    'route' => $model->performance->route,
                    'search_class' => $model->performance->search_class,
                    'merchant_id' => Yii::$app->getUser()->getId(),
                ]
            );
            $command->execute();
            $model->is_default = 1;
        } else {
            $model->is_default = 0;
        }
        $model->save(false);

        $request = Yii::$app->getRequest();
        if ($request->getIsPjax()) {
            return $this->runReferrerRequest(['t' => [
                'performance_id' => $id,
            ]]);
        } else {
            if ($request->getReferrer()) {
                $url_parts = parse_url($request->getReferrer());
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
                            'performance_id' => $id
                        ]
                    ]
                );
                $url_parts['query'] = http_build_query($params);
                return $this->redirect($url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query']);
            }

            return $this->redirect(['index', 't[performance_id]' => $id]);
        }
    }

    /**
     * Finds the PerformanceAdminSettings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $performance_id
     * @param integer $merchant_id
     * @return PerformanceAdminSettings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($performance_id, $merchant_id)
    {
        if (($model = PerformanceAdminSettings::find()
                ->innerJoinWith([
                    'merchant merchant',
                    'performance performance'
                ])
                ->pk($performance_id, $merchant_id)
                ->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('errors', 'Запрашиваемая страница не найдена.'));
        }
    }
}
