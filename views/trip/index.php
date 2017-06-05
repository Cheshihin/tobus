<?php
/*
 * Заказы на рейсе + данные рейса и выбранной машины
 */

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

\app\assets\AdminAsset::register($this);  // пока из админского ассета позаимстуем стили для таблицы


$this->title = 'Информация о рейсе '.date("d.m.Y", $trip->date).' '.date("H:i", $trip->date).' ('.$trip->start_time.', '.$trip->mid_time.', '.$trip->end_time.')';
$this->params['breadcrumbs'][] = $this->title;


$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
?>

<div id="trip-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить рейс', ['...'], ['class' => 'btn btn-success']) ?>
        </div>
        <!--
        ... class="pull-left"
        -->
    </div>
    <div></div>

    <div class="box-body box-table">
        <?= GridView::widget([
            'dataProvider' => $orderDataProvider,
            'filterModel' => $orderSearchModel,
            //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\CheckboxColumn'],
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                [
                    'attribute' => 'first_confirm_click_time',
                    'label' => 'Время подтверждения',
                    'content' => function ($model) {
                        return date('H:i', $model->first_confirm_click_time);
                    }
                ],
                [
                    'attribute' => 'price',
                    'content' => function ($model) {
                        return date('H:i', $model->price);
                    }
                ],
//                [
//                    'attribute' => 'status_id',
//                    'content' => function ($model) {
//                        if($model->status_id > 0) {
//                            return $model->status->name;
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $orderSearchModel,
//                        'status_id',
//                        ['' => 'Все'] + ArrayHelper::map(OrderStatus::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'date',
//                    'content' => function ($model) {
//                        return (empty($model->date) ? '' : date('d.m.Y', $model->date));
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'date',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
//                ],
//                [
//                    'attribute' => 'client_id',
//                    'content' => function ($model) {
//                        return $model->client->name;
//                    },
//                ],
//                'tr_id',
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
                        $orderSearchModel,
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
                        $orderSearchModel,
                        'point_id_to',
                        ['' => 'Все'] + $point_list,
                        ['class' => "form-control"]
                    )
                ],
                'price',
//                [
//                    'attribute' => 'is_free',
//                    'content' => function($model) {
//                        return ($model->is_free == 1 ? 'да' : 'нет');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $orderSearchModel,
//                        'is_free',
//                        ['' => 'Все', '1' => 'да', '0' => 'нет'],
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'places_count',
                    'label' => 'Мест'
                ],
                [
                    'attribute' => 'student_count',
                    'label' => 'Студ.',
                    'content' => function($model) {
                        return intval($model->student_count);
                    },
                ],
                [
                    'attribute' => 'child_count',
                    'label' => 'Дет.',
                    'content' => function($model) {
                        return intval($model->child_count);
                    },
                ],
                [
                    'attribute' => 'bag_count',
                    'label' => 'Сумки',
                    'content' => function($model) {
                        return intval($model->bag_count);
                    },
                ],
                [
                    'attribute' => 'suitcase_count',
                    'label' => 'Чемоданы',
                    'content' => function($model) {
                        return intval($model->suitcase_count);
                    },
                ],
                [
                    'attribute' => 'oversized_count',
                    'label' => 'Габариты',
                    'content' => function($model) {
                        return intval($model->oversized_count);
                    },
                ],

//                [
//                    'attribute' => 'is_not_places',
//                    'content' => function($model) {
//                        return ($model->is_not_places == 1 ? 'Без места' : '');
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $orderSearchModel,
//                        'is_not_places',
//                        ['' => 'Все', '1' => 'Без места', '0' => 'С местом'],
//                        ['class' => "form-control"]
//                    )
//                ],
//                'parent_id',
//                [
//                    'attribute' => 'time_getting_into_car',
//                    'content' => function ($model) {
//                        return (empty($model->time_getting_into_car) ? '' : date('d.m.Y H:i', $model->time_getting_into_car));
//                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'time_getting_into_car',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
//                ],
//                'comment',
//                [
//                    'attribute' => 'time_confirm',
//                    'content' => function ($model) {
//                        return (empty($model->time_confirm) ? '' : date('d.m.Y H:i', $model->time_confirm));
//                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'time_confirm',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
//                ],
//                'categ_id',
//                [
//                    'attribute' => 'time_sat',
//                    'content' => function ($model) {
//                        return (empty($model->time_sat) ? '' : date('d.m.Y H:i', $model->time_sat));
//                    },
//                    'filter' => DateTimePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'time_sat',
//                        'convertFormat' => true,
//                        'pluginOptions' => [
//                            'format' => 'dd.MM.yyyy hh:i',
//                            'autoclose' => true,
//                        ],
//                    ]),
//                ],
//                [
//                    'attribute' => 'created_at',
//                    'content' => function ($model) {
//                        return (empty($model->created_at) ? '' : date('d.m.Y', $model->created_at));
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'created_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
//                ],
//                [
//                    'attribute' => 'updated_at',
//                    'content' => function ($model) {
//                        return (empty($model->updated_at) ? '' : date('d.m.Y', $model->updated_at));
//                    },
//                    'filter' => DatePicker::widget([
//                        'model' => $orderSearchModel,
//                        'attribute' => 'updated_at',
//                        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
//                        'pluginOptions' => [
//                            'autoclose' => true,
//                            'format' => 'dd.mm.yyyy',
//                        ]
//                    ])
//                ],
//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    'template' => '{update} {delete}',
//                    'options' => ['style' => 'width: 50px;']
//                ],
            ],
        ]); ?>
    </div>
</div>
