<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\OrderSearch;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Helper;
use app\models\Trip;

/**
 * Рейсы
 *
 * !чтение данных по рейсам не должно происходить напрямую через Trip::find(), а должно происходить
 *  только через функции модели Trip
 */
class TripController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /*
      * Список рейсов
      */
    public function actionAjaxIndex($date)
    {
        Yii::$app->response->format = 'json';

        $direction_id = intval(Yii::$app->request->post('direction_id'));

        $correct_unixdate = strtotime($date);
        Trip::checkGenerateTrips($correct_unixdate);

        $trips_query = Trip::find();
        if($direction_id > 0) {
            $trips_query
                ->where(['>=', 'date', $correct_unixdate])
                ->andWhere(['<', 'date', $correct_unixdate + 86400])
                ->andWhere(['direction_id' => $direction_id]);
        }

        return $trips_query->all();
    }

    /*
     * Заказы на рейсе + выбор машины для рейса
     */
    public function actionTripOrders($trip_id) {

        $trip = Trip::findOne($trip_id);
        if($trip == null) {
            throw new ForbiddenHttpException('Рейс не найден');
        }

        $orderSearchModel = new OrderSearch();
        //$queryParams = Yii::$app->request->queryParams;
        //$queryParams['OrderSearch']['trip_id'] = $trip_id;
        //echo "<pre>"; print_r($queryParams); echo "</pre>";
        $orderDataProvider = $orderSearchModel->TripSearch(Yii::$app->request->queryParams, $trip_id);

        return $this->render('index', [
            'trip' => $trip,
            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
        ]);
    }
}
