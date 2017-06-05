<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\Point;
use kartik\field\FieldRange;

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="client-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить клиента', ['create'], ['class' => 'btn btn-success']) ?>
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
            'options' => ['class' => 'grid-view table-responsive'],
            'tableOptions' => [
                'class' => 'table table-condensed table-bordered table-hover'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                'mobile_phone',
                'home_phone',
                'alt_phone',
//                [
//                    'attribute' => 'last_point_from',
//                    'content' => function($model) {
//                        if($model->last_point_from > 0) {
//                            $point = $model->lastPointFrom;
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'last_point_from',
//                        ['' => 'Все'] + ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
//                        ['class' => "form-control"]
//                    )
//                ],
//                [
//                    'attribute' => 'last_point_to',
//                    'content' => function($model) {
//                        if($model->last_point_to > 0) {
//                            $point = $model->lastPointTo;
//                            $city = $point->city;
//                            return $point->name.' (г.'.$city->name.')';
//                        }else {
//                            return '';
//                        }
//                    },
//                    'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'last_point_to',
//                        ['' => 'Все'] + ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
//                        ['class' => "form-control"]
//                    )
//                ],
                [
                    'attribute' => 'rating',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'rating_from',
                        'attribute2' => 'rating_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'order_count',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'order_count_from',
                        'attribute2' => 'order_count_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
                ],
                [
                    'attribute' => 'prize_trip_count',
                    'filter' => FieldRange::widget([
                        'model' => $searchModel,
                        'template' => '{widget}',
                        'useAddons' => false,
                        'labelOptions' => ['style' => 'display:none;'],
                        'attribute1' => 'prize_trip_count_from',
                        'attribute2' => 'prize_trip_count_to',
                        'type' => FieldRange::INPUT_TEXT,
                        'separator' => '&rarr;',
                        'options1' => [
                            'placeholder' => 'мин',
                            'style' => 'min-width: 40px; padding: 6px 5px;'
                        ],
                        'options2' => [
                            'placeholder' => 'макс',
                            'style' => 'min-width: 44px; padding: 6px 5px;'
                        ],
                        'separatorOptions' => [
                            'class' => 'input-group-addon',
                            'style' => 'padding: 6px 5px;'
                        ]
                    ])
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

