<?php

namespace app\controllers;

use app\models\Direction;
use app\models\Order;
use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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


    /**
     * Ajax поиск клиента по номеру телефона
     */
    public function actionAjaxGetClient($mobile_phone, $direction_id)
    {
        Yii::$app->response->format = 'json';

        $client = Client::getClientByMobilePhone($mobile_phone);

        // ищеться последний заказ с данным клиентом и направлением, и если найден, то возвращаются точки заказа.
        $order = null;
        if($client != null && $direction_id > 0) {
            $order = Order::find()->where(['client_id' => $client->id, 'direction_id' => $direction_id])->one();
        }

        return [
            'success' => true,
            'order_id' => $order != null ? $order->id : null,
            'order' => $order,
            'client' => $client,
            'pointFrom' => ($order != null ? $order->pointFrom : ''),
            'pointTo' => ($order != null ? $order->pointTo : '')
        ];
    }


    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
