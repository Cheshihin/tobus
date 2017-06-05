<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use app\models\Client;
use app\models\OrderSearch;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Helper;
use yii\helpers\Url;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
     * Контент для модального окна создания заказа (записи пассажира)
     */
//    public function actionAjaxGetForm($day_code)
//    {
//        $order = new Order();
//        $client = new Client();
//
//        $order->date = Helper::getUnixtimeByDateCode($day_code);
//
//        return $this->renderAjax('form.php', [
//            'day_code' => $day_code,
//            'order' => $order,
//            'client' => $client
//        ]);
//    }

    public function actionAjaxGetForm($date = '')
    {
        Yii::$app->response->format = 'json';

        $order = new Order();
        $client = new Client();

        /*$order->date = Helper::getUnixtimeByDateCode($day_code);

        return $this->renderAjax('form.php', [
            'day_code' => $day_code,
            'order' => $order,
            'client' => $client
        ]);*/

        $order->date = !empty($date) ? strtotime($date) : '';
        $day_code = Helper::getDayCode($date);

        return [
            'success' => true,
            'html' => $this->renderAjax('form.php', [
                'day_code' => $day_code,
                'order' => $order,
                'client' => $client
            ]),
            //'day_code' => $day_code,
            'class' => Helper::getClassByDayCode($day_code),
            'title' => Helper::getOrderCreateTitle($day_code)
        ];
    }

    /*
     * Создание заказа (записи пассажира если необходимо)
     */
    public function actionAjaxCreateOrder()
    {
        Yii::$app->response->format = 'json';

        $order = new Order();
        $post = Yii::$app->request->post();

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null) {
            $client = new Client();
        }

        if($post['submit_button'] == 'confirm-button')
        {
            $order->scenario = 'confirm_button_create';
            $order->first_confirm_click_time = time();
            $order->first_confirm_clicker_id = Yii::$app->user->identity->id;

        }elseif($post['submit_button'] == 'writedown-button')
        {
            $order->scenario = 'writedown_button_create';
            $order->first_writedown_click_time = time();
            $order->first_writedown_clicker_id = Yii::$app->user->identity->id;

        }else {
            throw new ForbiddenHttpException('Данные формы отправлены нажатием на неизвестную кнопку');
        }

        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            if(!$client->save()) {
                throw new ErrorException('Не удалось сохранить клиента');
            }

            $order->client_id = $client->id;

            if(!$order->save()) {
                throw new ErrorException('Не удалось сохранить заказ');
            }

            return [
                'success' => true,
                'order' => $order,
                'client' => $client,
                'form_new_action' => Url::to(['ajax-update-order', 'id' => $order->id]),
                'form_html' => $this->renderPartial('form.php', [
                    'day_code' => $order->date,
                    'order' => $order,
                    'client' => $client
                ])
            ];
        }else {

            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
                'client_errors' => $client->validate() ? '' : $client->getErrors(),
            ];
        }
    }

    public function actionTest()
    {
        phpinfo();
    }

    /*
     * Обновление заказа
     */
    public function actionAjaxUpdateOrder($id)
    {
        Yii::$app->response->format = 'json';

        $order = Order::findOne($id);
        if($order == null) {
            throw new ErrorException('Заказ не найден');
        }

        $post = Yii::$app->request->post();
        //echo "post:<pre>"; print_r($post); echo "</pre>";

        // ищется клиент по номеру телефона, если не находиться то создается новый
        $client = null;
        if(isset($post['Client']['mobile_phone'])) {
            $client = Client::getClientByMobilePhone($post['Client']['mobile_phone']);
        }
        if($client == null) {
            $client = new Client();
        }


        if($post['submit_button'] == 'confirm-button')
        {
            $order->scenario = 'confirm_button_update';
            $order->first_confirm_click_time = time();
            $order->first_confirm_clicker_id = Yii::$app->user->identity->id;

        }elseif($post['submit_button'] == 'writedown-button')
        {
            $order->scenario = 'writedown_button_update';
            $order->first_writedown_click_time = time();
            $order->first_writedown_clicker_id = Yii::$app->user->identity->id;

        }else {
            throw new ForbiddenHttpException('Данные формы отправлены нажатием на неизвестную кнопку');
        }

        if($client->load($post) && $order->load($post) && $client->validate() && $order->validate())
        {
            if(!$client->save()) {
                throw new ErrorException('Не удалось сохранить клиента');
            }

            $order->client_id = $client->id;

            if(!$order->save()) {
                throw new ErrorException('Не удалось сохранить заказ');
            }

            return [
                'success' => true,
                'order' => $order,
                'client' => $client
            ];
        }else {

            return [
                'success' => false,
                'order_errors' => $order->validate() ? '' : $order->getErrors(),
                'client_errors' => $client->validate() ? '' : $client->getErrors(),
            ];
        }
    }


    /*
     * Функция возвращает цену заказа
     */
    public function actionAjaxGetCalculatePrice()
    {
        Yii::$app->response->format = 'json';

        $model = new Order();

        $model->scenario = 'calculate_price';
        if ($model->load(Yii::$app->request->post())) {
            return [
                'success' => true,
                'price' => $model->calculatePrice, // а если пришли пустые значения, то как сработает расчет?
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
