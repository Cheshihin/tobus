<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\City;
use app\models\CitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Point;
use app\models\PointSearch;

/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller
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
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();
        if (Yii::$app->request->isAjax)
        {
            // данные из таблицы Points
            return $this->render('create', [
                'model' => $model,
                'pointSearchModel' => [],//$pointSearchModel,
                'pointDataProvider' => [], //$pointDataProvider,
            ]);

        }else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'pointSearchModel' => [], //$pointSearchModel,
                    'pointDataProvider' => [], //$pointDataProvider,
                ]);
            }
        }
    }

    /**
     * Updates an existing City model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax)
        {
            $pointSearchModel = new PointSearch();
            $queryParams = Yii::$app->request->queryParams;

            // косяк $.pjax.reload - то отправляет $queryParams['city_id'], то отправляет $queryParams['PointSearch']['city_id']
            if(isset($queryParams['city_id']) && !empty($queryParams['city_id'])) {
                $queryParams['PointSearch']['city_id'] = $queryParams['city_id'];
            }

            $pointDataProvider = $pointSearchModel->search($queryParams);

            // данные из таблицы Points
            return $this->render('update', [
                'model' => $model,
                'pointSearchModel' => $pointSearchModel,
                'pointDataProvider' => $pointDataProvider,
            ]);

        }else {

            $pointSearchModel = new PointSearch();
            $queryParams = Yii::$app->request->queryParams;
            $queryParams['PointSearch']['city_id'] = $id;
            $pointDataProvider = $pointSearchModel->search($queryParams);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'pointSearchModel' => $pointSearchModel,
                    'pointDataProvider' => $pointDataProvider,
                ]);
            }
        }
    }

    /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAjaxDelete($id)
    {
        // если у города есть точки остановки, то запрещаем удалять
        $point = Point::find()->where(['city_id' => $id])->one();
        if($point != null) {
            Yii::$app->response->format = 'json';
            throw new ForbiddenHttpException('Нельзя удалить город, так как у него есть точки остановок (удалите вначале все точки остановок)');
        }

        $this->findModel($id)->delete();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
