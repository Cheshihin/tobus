<?php

namespace app\controllers;

use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;
use app\models\Direction;

/**
 * PointController implements the CRUD actions for Point model.
 */
class PointController extends Controller
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
     * Список точек отправления и точек прибытия
     */
    public function actionAjaxGetPoints($direction_id)
    {
        Yii::$app->response->format = 'json';

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Направление не найдено');
        }

        return [
            'points_from' => Point::find()->where(['city_id' => $direction->city_from, 'active' => 1])->all(),
            'points_to' => Point::find()->where(['city_id' => $direction->city_to, 'active' => 1])->all(),
        ];
    }

    /*
     * Функция возвращает данные точки
     */
    public function actionAjaxGetPoint($point_id)
    {
        Yii::$app->response->format = 'json';

        $point = Point::findOne($point_id);
        if($point == null) {
            throw new ForbiddenHttpException('Точка не найдена');
        }

        return $point;
    }

    /*
     * Функция возвращает результат поиска точек отправления для SelectWidget`а или скажем для kartik-элемента формы
     */
    public function actionAjaxFormElemPoints($is_point_from)
    {
        Yii::$app->response->format = 'json';

        $out['results'] = [];

        $search = Yii::$app->getRequest()->post('search');
        $direction_id = intval(Yii::$app->getRequest()->post('direction_id'));

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Необходимо выбрать направление');
        }

        if($is_point_from == 1) {
            $points_query = Point::find()->where(['city_id' => $direction->city_from, 'active' => 1]);
        }else {
            $points_query = Point::find()->where(['city_id' => $direction->city_to, 'active' => 1]);
        }

        if($search != '') {
            $points_query->andWhere(['LIKE', 'name', $search]);
        }

        $points = $points_query->all();

        $out['results'] = [];
        foreach($points as $point) {
            $out['results'][] = [
                'id' => $point->id,
                'text' => $point->name,
            ];
        }

        return $out;
    }

    /*
     * Функция сохранение новой точки
     */
    public function actionAjaxCreatePoint($is_point_from, $direction_id, $new_value)
    {
        Yii::$app->response->format = 'json';

        $direction = Direction::findOne($direction_id);
        if($direction == null) {
            throw new ForbiddenHttpException('Необходимо выбрать направление');
        }

        $new_value = trim(htmlspecialchars($new_value));
        if($new_value == '') {
            throw new ForbiddenHttpException('Передано пустое значение нового элемента');
        }

        $point = new Point();
        $point->name = $new_value;

        if($is_point_from == 1) {
            $point->city_id = $direction->city_from;
        }else {
            $point->city_id = $direction->city_to;
        }

        if(!$point->save(false)) {
            throw new ErrorException('Не удалось создать нового элемента');
        }

        return [
            'success' => true,
            'point' => $point
        ];
    }
}
