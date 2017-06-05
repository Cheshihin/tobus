<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\models\Point;
use app\models\City;
use kartik\date\DatePicker;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->registerJsFile('js/admin/pages.js', ['depends' => 'app\assets\AdminAsset']);
?>

<?php $form = ActiveForm::begin([
    'id' => 'city-form',
    'options' => [
        'city-id' => $model->id
    ]
]); ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-address-book-o"></i>
            Основная информация
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 form-group form-group-sm">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить и выйти', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
if(!$model->isNewRecord)
{ ?>
    <div id="points-list" class="box box-solid">

        <div class="box-header scroller with-border">
            <h3 class="box-title">
                <i class="fa fa-address-book-o"></i>
                Точки остановок
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>


        <div class="box-body box-table">

            <?= Html::a('<i class="fa fa-plus"></i> Добавить точку', Url::to(['/admin/point/ajax-create', 'city_id' => $model->id]), ['id'=>'add-point', 'class' => 'btn btn-success']) ?>
            <br /><br />

            <?php Pjax::begin([
                'id' => 'points-grid'
            ]) ?>

            <?= GridView::widget([
                'dataProvider' => $pointDataProvider,
                'filterModel' => $pointSearchModel,
                //'layout' => '{items}<span class="pull-right text-muted">{summary}</span>',
                'options' => ['class' => 'grid-view table-responsive'],
                'tableOptions' => [
                    'class' => 'table table-condensed table-bordered table-hover'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'name',

                    [
                        'attribute' => 'city_id',
                        'content' => function($model) {
                            return $model->city->name;
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'city_id',
                            ['' => 'Все'] + ArrayHelper::map(City::find()->all(), 'id', 'name'),
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'alias',
                        'content' => function($model) {
                            return (empty($model->alias) ? '' : $model->alias);
                        },
                    ],
                    [
                        'attribute' => 'point_of_arrival',
                        'content' => function($model) {
                            return ($model->point_of_arrival == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'point_of_arrival',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],

                    [
                        'attribute' => 'critical_point',
                        'content' => function($model) {
                            return ($model->critical_point == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'critical_point',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],
                    [
                        'attribute' => 'created_at',
                        'content' => function ($model) {
                            return date('d.m.Y', $model->created_at);
                        },
                        'filter' => DatePicker::widget([
                            'model' => $pointSearchModel,
                            'attribute' => 'created_at',
                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ]
                        ]),
                    ],
                    [
                        'attribute' => 'updated_at',
                        'content' => function ($model) {
                            return (!empty($model->updated_at) ? date('d.m.Y', $model->updated_at) : '');
                        },
                        'filter' => DatePicker::widget([
                            'model' => $pointSearchModel,
                            'attribute' => 'updated_at',
                            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'active',
                        'content' => function($model) {
                            return ($model->active == 1 ? 'Да' : 'Нет');
                        },
                        'filter' => Html::activeDropDownList(
                            $pointSearchModel,
                            'active',
                            ['' => 'Все', 1 => 'Да', 0 => 'Нет'],
                            ['class' => "form-control"]
                        )
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'options' => ['style' => 'width: 50px;'],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    Url::to(['/admin/point/ajax-update', 'id' => $model->id]),
                                    ['aria-label' => 'Редактировать', 'class' => "edit-point"]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['/admin/point/ajax-delete', 'id' => $model->id]),
                                    [
                                        'aria-label' => 'Удалить',
    //                                    'data-pjax' => "1",
    //                                    'data-confirm' => "Вы уверены, что хотите удалить этот элемент?",
    //                                    'data-method' => "post"
                                        'class' => "delete-point"
                                    ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>

<?php } ?>