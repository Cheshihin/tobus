<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\Trip;
use app\components\Helper;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($date = null)
    {

//        $today = date('d.m.Y');
//        $unixtoday = strtotime($today);
//
//        echo "unixtoday=".date('d.m.Y H:i', $unixtoday);

//        $date = '31.05.2017';
//        $unixdate = strtotime($date);
//
//        $result = Trip::createStandartTripList($unixdate);
//        echo "result=$result <br />";

        return $this->render('index');
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $model->rememberMe = 1;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $this->layout = 'login';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /*
     * Функция возвращает строку времени для часов в верхнем меню
     *
     * @return string
     */
    public function actionGetAjaxTime() {

        Yii::$app->response->format = 'json';

        return [
            'success' => true,
            'time' => Helper::getMainDate(time(), 1)
        ];
    }

    public function actionTest() {
        echo date('d.m.Y H:i:s', 1468443600);
    }



    // ------------------  СОБЫТИЯ НИЖЕ НЕ ПРОВЕРЯЛ -------------------


    /*public function actionAdmin() {
        $role = Yii::app()->user->role;
        if ($role !== 'root' & $role !== 'admin' & $role !== 'editor') {
            Yii::app()->user->setFlash('warning', '<strong>Ошибка!</strong> У вас не хватает прав, пожалуйста, авторизируйтесь.');
            Yii::app()->user->setReturnUrl('/site/admin');
            $this->actionLogin();
        } else {
            Yii::app()->request->redirect('/admin');
        }
    }*/

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError($date) {
        /*if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }*/
        $error = '';
        $this->render('error', $error);
    }

}
