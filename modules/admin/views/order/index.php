<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use app\models\OrderStatus;
use yii\helpers\ArrayHelper;
use app\models\Tariff;
use app\models\Point;
use app\models\Trip;
use app\models\Order;

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
?>
<div id="order-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить заказ', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <!--
        ... class="pull-left"
        -->
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'status_id',
                    'content' => function ($model) {
                        if($model->status_id > 0) {
                            return $model->status->name;
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status_id',
                        ['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'date',
                    'content' => function ($model) {
                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'date',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],
                [
                    'attribute' => 'client_id',
                    'content' => function ($model) {
                        return $model->client->name;
                    },
                ],
//                [
//                    'attribute' => 'alt_fio',
//                    'content' => function($model) {
//                        return (empty($model->alt_fio) ? '' : $model->alt_fio);
//                    }
//                ],
                'tr_id',
                [
                    'attribute' => 'point_id_from',
                    'content' => function($model) {
                        if($model->point_id_from > 0) {
                            $point = $model->pointFrom;
                            $city = $point->city;
                            return $point->name.' (г.'.$city->name.')';
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'point_id_from',
                        ['' => 'Все'] + $point_list,
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'point_id_to',
                    'content' => function($model) {
                        if($model->point_id_to > 0) {
                            $point = $model->pointTo;
                            $city = $point->city;
                            return $point->name.' (г.'.$city->name.')';
                        }else {
                            return '';
                        }
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'point_id_to',
                        ['' => 'Все'] + $point_list,
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'is_free',
                    'content' => function($model) {
                        return ($model->is_free == 1 ? 'да' : 'нет');
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'is_free',
                        ['' => 'Все', '1' => 'да', '0' => 'нет'],
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'trip_id',
                    'content' => function($model) {
                        return $model->trip->name;
                    },
                ],
                'places_count',
                'student_count',
                'child_count',
                'bag_count',
                'suitcase_count',
                'oversized_count',
//                [
//                    'attribute' => 'baggage',
//                    'content' => function($model) {
//                        return $model->baggageName;
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'baggage',
//                        ['' => 'Все', ] + Order::$baggageList,
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'is_not_places',
//                    'content' => function($model) {
//                        return ($model->is_not_places == 1 ? 'Без места' : '');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'is_not_places',
//                        ['' => 'Все', '1' => 'Без места', '0' => 'С местом'],
//                        ['class' => "form-control"]
//                    )
//                ],
                'parent_id',
                [
                    'attribute' => 'time_getting_into_car',
                    'content' => function ($model) {
                        return (empty($model->time_getting_into_car) ? '' : date('d.m.Y H:i', $model->time_getting_into_car));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_getting_into_car',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                'comment',
                //'contacts',
                [
                    'attribute' => 'time_confirm',
                    'content' => function ($model) {
                        return (empty($model->time_confirm) ? '' : date('d.m.Y H:i', $model->time_confirm));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_confirm',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                'categ_id',
                [
                    'attribute' => 'time_sat',
                    'content' => function ($model) {
                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
                    },
                    'filter' => DateTimePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'time_sat',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd.MM.yyyy hh:i',
                            'autoclose' => true,
                        ],
                    ]),
                ],
                [
                    'attribute' => 'created_at',
                    'content' => function ($model) {
                        return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],
                [
                    'attribute' => 'updated_at',
                    'content' => function ($model) {
                        return (empty($model->updated_at) ? '' : date('d.m.Y', $model->updated_at));
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy',
                        ]
                    ])
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'options' => ['style' => 'width: 50px;']
                ],
            ],
        ]); ?>
    </div>
</div>
