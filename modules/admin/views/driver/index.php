<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use kartik\date\DatePicker;
use app\models\Transport;

$this->title = 'Водители';
$this->params['breadcrumbs'][] = $this->title;

$arTransport = ArrayHelper::map(Transport::find()->all(), 'id', 'name');
?>
<div id="driver-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?= Html::a('<i class="fa fa-plus"></i> Добавить водителя', ['create'], ['class' => 'btn btn-success']) ?>
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

                'fio',
                'mobile_phone',
                'home_phone',
                [
                    'attribute' => 'primary_transport_id',
                    'content' => function($model) {
                        return $model->primaryTransport->name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'primary_transport_id',
                        ['' => 'Все'] + $arTransport,
                        ['class' => "form-control"]
                    )
                ],
                [
                    'attribute' => 'secondary_transport_id',
                    'content' => function($model) {
                        return $model->secondaryTransport->name;
                    },
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'secondary_transport_id',
                        ['' => 'Все'] + $arTransport,
                        ['class' => "form-control"]
                    )
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
