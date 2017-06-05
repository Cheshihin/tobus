<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

$this->title = 'Тарифы';
$this->params['breadcrumbs'][] = $this->title;
?>
<p class="text-danger">Тарифы вшиты в код, поэтому нельзя создавать новые тарифы, удалять тарифы и нельзя изменять псевдонимы </p><br />

<div id="tariff-page" class="box box-default" >
    <div class="box-header scroller with-border">
        <div class="pull-left">
            <?php //echo Html::a('<i class="fa fa-plus"></i> Добавить тариф', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <!--
        ... class="pull-left"
        -->
    </div>
    <div></div>

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

            'name',
            'alias',
            [
                'attribute' => 'cost',
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
//            [
//                'attribute' => 'active',
//                'content' => function($model) {
//                    return ($model->active == 1 ? 'да' : 'нет');
//                },
//                'filter' => Html::activeDropDownList(
//                    $searchModel,
//                    'active',
//                    ['' => 'Все', '1' => 'Да', '0' => 'Нет'],
//                    ['class' => "form-control"]
//                )
//            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'options' => ['style' => 'width: 30px;']
            ],
        ],
    ]); ?>
</div>
