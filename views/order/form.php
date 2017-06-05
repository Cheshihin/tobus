<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use \kartik\date\DatePicker;
use kartik\time\TimePicker;
use app\models\OrderStatus;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Tariff;
use app\models\Point;
use app\models\Trip;
use app\models\Order;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\models\Direction;
use app\models\InformerOffice;
use yii\helpers\Url;
use app\widgets\SelectWidget;
use kartik\money\MaskMoney;

$point_list = ArrayHelper::map(Point::find()->where(['active' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

//echo "order<pre>"; print_r($order); echo "</pre>";
?>

<div class="order-form">

    <?php $form = ActiveForm::begin([
        'id' => 'order-client-form',
        'action' => ($order->id > 0 ? Url::to(['ajax-update-order', 'id' => $order->id]) : Url::to(['ajax-create-order'])),
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]); ?>

    <div class="row">
        <div class="col-sm-1 first-col">
            <label class="label-horizontal">Дата</label>
        </div>

        <div class="col-sm-3 mini-side-padding nowrap">
            <div class="form-group field-order-date required">
                <?php
                if($order->date > 0 && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i', $order->date)) {
                    $order->date = date("d.m.Y", $order->date);
                }
                echo $form->field($order, 'date', ['errorOptions' => ['style' => 'display:none;']])
                    ->widget(kartik\date\DatePicker::classname(), [
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'class' => ''
                        ],
                        'options' => [
                            'id' => 'date',
                            //'style' => 'height: 24px; padding: 6px 5px; width: 80px;',
                            //'disabled' => in_array($day_code, ['today', 'tomorrow']),
                            //'aria-required' => 'true',
                            //'placeholder' => '10.05.2017'
                        ]
                    ])
                    ->widget(\yii\widgets\MaskedInput::class, [
                        'clientOptions' => [
                            'alias' =>  'dd.mm.yyyy',
                        ],
                        'options' => [
                            'id' => 'date',
                            'style' => 'width: 80px;',
                            'disabled' => (in_array($day_code, ['today', 'tomorrow']) || !empty($order->first_confirm_click_time)),
                            'aria-required' => 'true',
                            'placeholder' => '10.05.2017'
                        ]
                    ])
                    ->label(false);
                ?>
            </div>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">НПР</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($order, 'direction_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList([0 => '---'] + ArrayHelper::map(Direction::find()->all(), 'id', 'sh_name'), [
                        'id' => 'direction',
                        'class' => 'checkbox',
                        'disabled' => !empty($order->first_confirm_click_time),
                    ])
                    ->label(false); ?>
            </div>
        </div>

        <div class="col-sm-2 mini-side-padding nowrap">
            <label class="label-horizontal">Рейс</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?php
                if($order->direction_id > 0) {
                    echo $form->field($order, 'trip_id', ['errorOptions' => ['style' => 'display:none;']])
                        ->dropDownList(['' => '---'] + ArrayHelper::map(Trip::find()->all(), 'id', 'name'), [
                            'id' => 'trip',
                            'class' => 'checkbox',
                            'disabled' => !empty($order->first_confirm_click_time),
                        ])
                        ->label(false);
                }else {
                    echo $form->field($order, 'trip_id', ['errorOptions' => ['style' => 'display:none;']])
                        ->dropDownList(['' => '---'], ['id' => 'trip', 'class' => 'checkbox', 'disabled' => true])
                        ->label(false);
                }
                ?>
            </div>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding nowrap">
            <input id="informer-office-disable" type="checkbox" class="label-horizontal"> <label class="label-horizontal" style="margin-top: 10px;">сброс</label>
            <div class="elem-horizontal" style="height: 30px;">
                <?= $form->field($order, 'informer_office_id', ['errorOptions' => ['style' => 'display:none;']])
                    ->dropDownList(['' => '---'] + ArrayHelper::map(InformerOffice::find()->all(), 'id', 'name'), ['class' => 'checkbox', 'disabled' => true])
                    ->label(false); ?>
            </div>
        </div>
    </div>

    <div class="yellow-line">- Номер с которого звоните?</div>
    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Моб. основной</label>
            <?= $form->field($client, 'mobile_phone')
                ->textInput(['class' => 'input-text'])->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'options' => ['disabled' => ($order->direction_id > 0 ? false : true)],
                    'clientOptions' => [
                        'placeholder' => '*',
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Домашний</label>
            <?= $form->field($client, 'home_phone')
                ->textInput(['class' => 'input-text', 'placeholder' => '8-495-1234567', 'disabled' => true])
                ->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    //'charMap' => '*',
                    'mask' => '+7-999-999-9999',
                    'options' => ['disabled' => ($order->direction_id > 0 ? false : true)],
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Другой</label>
            <?= $form->field($client, 'alt_phone')
                ->textInput(['class' => 'input-text', 'disabled' => true])
                ->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'options' => ['disabled' => ($order->direction_id > 0 ? false : true)],
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 first-col">
            <label class="label-horizontal">ФИО</label>
        </div>
        <div class="col-sm-7 mini-side-padding nowrap">
            <?= $form->field($client, 'name', ['errorOptions' => ['style' => 'display:none;']])
                ->textInput(['class' => 'input-text', 'placeholder' => 'Иванов Иван Иваныч', 'disabled' => ($order->direction_id > 0 ? false : true)])->label(false) ?>
        </div>
    </div>



    <div class="yellow-line" style="margin-top: 5px;">- Сколько человек поедет? Есть ли студенты и дети? Будет ли багаж?</div>
    <div class="row">
        <div class="col-sm-1 first-col nowrap">
            <?php
            echo Html::activeCheckbox($order, 'is_not_places', ['label' => '', 'id' => 'places-count-disable', 'style'=>"margin-top: -20px;", 'class' => 'label-horizontal']);
            ?>
            <label class="label-horizontal" style="text-align:left; vertical-align: bottom; white-space: normal; margin-top: 20px; font-size: 11px;">Без места</label>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Мест</label>
            <?php //= Html::activeTextInput($order, 'places_count', ['class' => 'input-text', 'placeholder' => '100']) ?>
            <?= $form->field($order, 'places_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'clientOptions' => [
                        'showMaskOnFocus' => false,
                        'showMaskOnHover' => false
                    ],
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Студ.</label>
            <?php //= Html::activeTextInput($order, 'student_count', ['class' => 'input-text', 'placeholder' => '10']) ?>
            <?= $form->field($order, 'student_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Дет.</label>
            <?php //= Html::activeTextInput($order, 'child_count', ['class' => 'input-text', 'placeholder' => '10']) ?>
            <?= $form->field($order, 'child_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Сумки</label>
            <?php //= Html::activeTextInput($order, 'bag_count', ['class' => 'input-text', 'placeholder' => '10']) ?>
            <?= $form->field($order, 'bag_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Чемод.</label>
            <?php //= Html::activeTextInput($order, 'suitcase_count', ['class' => 'input-text', 'placeholder' => '10']) ?>
            <?= $form->field($order, 'suitcase_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>
        <div class="col-sm-1 mini-side-padding">
            <label class="label-vertical">Негабариты</label>
            <?php //= Html::activeTextInput($order, 'oversized_count', ['class' => 'input-text', 'placeholder' => '10']) ?>
            <?= $form->field($order, 'oversized_count')
                ->textInput(['class' => 'input-text'])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '999',
                    'options' => ['class' => "form-control", 'disabled' => $order->is_not_places ? true : false]
                ])
                ->label(false);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding nowrap">
            <?php
            $current_user = Yii::$app->user->identity;
            $current_user_role = $current_user->userRole;

            if($current_user_role['alias'] == 'admin') { ?>
                <input id="order-use_fix_price" name="Order[use_fix_price]" type="checkbox" style="vertical-align: bottom; margin-bottom: 3px;" class="label-horizontal"> <label class="label-horizontal" style="vertical-align: bottom;">фикс.</label>
                <div class="elem-horizontal" style="height: 48px;">
                    <label class="label-vertical">Фикс. цена</label>
                    <?php //= Html::activeTextInput($order, 'price', ['class' => 'input-text', 'disabled' => true]) ?>
                    <?= $form->field($order, 'price')
                        ->textInput(['class' => 'input-text'])
                        ->widget(MaskMoney::class, [
                            'pluginOptions' => [
                                'suffix' => '',
                                'affixesStay' => true,
                                'thousands' => ' ',
                                'decimal' => '',
                                'precision' => 0,
                                'allowZero' => true,
                                'allowNegative' => false,
                            ],
                            'options' => [
                                'class' => 'input-text',
                                'disabled' => true
                            ]
                        ])
                        ->label(false);
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-11 mini-side-padding">
            <label class="label-vertical">Примечания к заказу</label>
            <?= $form->field($order, 'comment', ['errorOptions' => ['style' => 'display:none;']])
                ->textarea(['class' => 'input-text'])->label(false);
            ?>
        </div>
    </div>


    <div class="yellow-line" style="margin-top: 5px;">- Дополнительные телефоны заказа (если более 2 человек)</div>
    <div class="row">
        <div class="col-sm-1 first-col"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 1</label>
            <?= $form->field($order, 'additional_phone_1', ['errorOptions' => ['style' => 'display:none;']])
                ->textInput(['class' => 'input-text'])->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 2</label>
            <?= $form->field($order, 'additional_phone_2', ['errorOptions' => ['style' => 'display:none;']])
                ->textInput(['class' => 'input-text'])->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>

        <div class="col-sm-1"></div>
        <div class="col-sm-3 mini-side-padding">
            <label class="label-vertical">Доп. тел. 3</label>
            <?php //= Html::activeTextInput($order, 'additional_phone_3', ['class' => 'input-text']) ?>
            <?= $form->field($order, 'additional_phone_3', ['errorOptions' => ['style' => 'display:none;']])
                ->textInput(['class' => 'input-text'])->label(false)
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '+7-999-999-9999',
                    'clientOptions' => [
                        'placeholder' => '*'
                    ]
                ]);
            ?>
        </div>
    </div>


    <div class="yellow-line" style="margin-top: 5px;">- Откуда поедете?</div>
    <div class="row">
        <div class="col-sm-1 first-col">
            <label class="label-horizontal">Откуда</label>
        </div>
        <div class="col-sm-4 mini-side-padding nowrap">
            <?php
            echo $form->field($order, 'point_id_from')->widget(SelectWidget::className(), [
                'initValueText' => ($order->point_id_from > 0 ? $order->pointFrom->name : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                    'id' => 'point_id_from',
                ],
                'ajax' => [
                    'url' => '/point/ajax-form-elem-points?is_point_from=1',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            direction_id: $("#direction").val()
                        };
                    }'),
//                    'afterRequest' => new JsExpression('function(response) {
//                        console.log("response"); console.log(response);
//
//                        if (response.results.length > 0) {
//
//                        }
//                    }'),
//                    'afterSelect' => new JsExpression('function(obj, value, text) {
//                        //console.log("value="+value);
//                    }'),
                ],
                'add_new_value_url' => new JsExpression('function(params) {

                    var direction_id = $("#direction").val();
                    var new_value = $("#point_id_from").parents(".sw-element").next(".sw-outer-block").find(".sw-search").val();

                    return "/point/ajax-create-point?is_point_from=1&direction_id="+direction_id+"&new_value="+new_value;
                }'),
            ])->label(false);
            ?>
        </div>
        <div class="col-sm-2"></div>

        <div class="col-sm-4 first-col" style="width: 32%;">
            <label class="label-horizontal" style="margin-top: 0;">Время прибытия поезда /<br /> посадки самолета</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="width: 9.6%;">
            <?php
            $order->time_air_train_arrival = '';

            echo $form->field($order, 'time_air_train_arrival')
            ->widget(\yii\widgets\MaskedInput::class, [
                'mask' => '99 : 99',
                'options' => [
                    'style' => 'width: 100%; text-align: center;',
                    'disabled' => true
                ],
                'clientOptions' => [
                    'placeholder' => '_'
                ]
            ])->label(false);
            ?>
        </div>
    </div>

    <div class="yellow-line">- Куда поедете?</div>
    <div class="row">
        <div class="col-sm-1 first-col">
            <label class="label-horizontal">Куда</label>
        </div>
        <div class="col-sm-4 mini-side-padding nowrap">
            <?php
            echo $form->field($order, 'point_id_to')->widget(SelectWidget::className(), [
                'initValueText' => ($order->point_id_to > 0 ? $order->pointTo->name : ''),
                'options' => [
                    'placeholder' => 'Введите название...',
                    'id' => 'point_id_to',
                ],
                'ajax' => [
                    'url' => '/point/ajax-form-elem-points?is_point_from=0',
                    'data' => new JsExpression('function(params) {
                        return {
                            search: params.search,
                            direction_id: $("#direction").val()
                        };
                    }')
                ],
                'add_new_value_url' => new JsExpression('function(params) {

                    var direction_id = $("#direction").val();
                    var new_value = $("#point_id_to").parents(".sw-element").next(".sw-outer-block").find(".sw-search").val();

                    return "/point/ajax-create-point?is_point_from=0&direction_id="+direction_id+"&new_value="+new_value;
                }'),
            ])->label(false);
            ?>
        </div>
        <div class="col-sm-2"></div>

        <div class="col-sm-4 first-col" style="width: 32%;">
            <label class="label-horizontal" style="margin-top: 0;">Время отправления поезда /<br /> начало регистрации авиарейса</label>
        </div>
        <div class="col-sm-1 mini-side-padding nowrap" style="width: 9.6%;">
            <?php
            echo $form->field($order, 'time_air_train_departure')
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '99 : 99',
                    'options' => [
                        'style' => 'width: 100%; text-align: center;',
                        'disabled' => true
                    ],
                    'clientOptions' => [
                        'placeholder' => '_'
                    ]
                ])->label(false);
            ?>
        </div>
    </div>

    <div class="yellow-line">- Место вам точно есть.</div>
    <div class="row">
        <div class="col-sm-1 first-col" style="width: 13.5%;">
            <?php
            $order->time_confirm = '';

            if($order->time_getting_into_car > 0) {
                $order->time_getting_into_car = date("H : i", $order->time_getting_into_car);
            }
            echo $form->field($order, 'time_getting_into_car', ['errorOptions' => ['style' => 'display:none;']])
                ->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '99 : 99',
                    'options' => [
                        'style' => 'width: 100%; padding-top: 3px; text-align: center;',
                        'disabled' => !empty($order->first_confirm_click_time)
                    ],
                    'clientOptions' => [
                        'placeholder' => '_'
                    ]
                ])->label(false);
            ?>

            <?php if($order->first_confirm_click_time) { ?>
                <input id='confirm-button' type="button" value="Подтверждено" class="btn btn-success" style="font-size: 10px; padding: 3px 2px; width: 100%;" disabled="disabled" />
            <?php }else { ?>
                <input id='confirm-button' type="button" value="Подтвердить" class="btn btn-default" style="font-size: 10px; padding: 3px 2px; width: 100%;" />
            <?php } ?>
        </div>

        <div class="col-sm-10">
            <?=  $form->field($order, 'radio_group_1')
                ->radioList(
                    $order->radioGroup1,
                    [
                        'item' => function ($index, $label, $name, $checked, $value) use($order) {
                            return '<label style="font-weight: normal; margin: 0;">' .
                            Html::radio($name, $checked, [
                                'value' => $value,
                                'style' => "vertical-align:bottom; margin:0;",
                                'disabled' => empty($order->first_confirm_click_time),
                                'class' => empty($order->first_confirm_click_time) ? 'disabled' : ''
                            ]) . ' <span id="radio_group_1_'.$value.'" text="'.$label.'">'.$label.'</span></label>';
                        },
                        'class' => empty($order->first_confirm_click_time) ? 'disabled' : ''
                    ]
                )
                ->label(false);
            ?>
            <?= $form->field($order, 'radio_group_2')
                ->radioList(
                    $order->radioGroup2,
                    [
                        'item' => function ($index, $label, $name, $checked, $value) use($order) {
                            return '<label style="font-weight: normal; margin: 0;">' .
                            Html::radio($name, $checked, [
                                'value' => $value,
                                'style' => "vertical-align:bottom; margin:0;",
                                'disabled' => !empty($order->first_confirm_click_time),
                                'class' => !empty($order->first_confirm_click_time) ? 'disabled' : ''
                            ]) . ' <span id="radio_group_2_'.$value.'" text="'.$label.'">'.$label.'</span></label>';
                        },
                        'class' => !empty($order->first_confirm_click_time) ? 'disabled' : ''
                    ]
                )
                ->label(false);
            ?>
        </div>
    </div>
    <div class="yellow-line">Стоимость проезда составит: <span id="price">0</span> рублей. </div>

    <div class="row">
        <div class="col-sm-1  first-col" style="width: 13.5%;">&nbsp;</div>
        <div class="col-sm-10">
            <?php
            echo $form->field($order, 'radio_group_3')
                ->radioList(
                    $order->radioGroup3,
                    [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<label style="font-weight: normal; margin: 0;">' .
                            Html::radio($name.'-'.$value, $checked, [
                                'value' => $value,
                                'style' => "vertical-align:bottom; margin:0;",
                                'disabled' => true
                            ]) . ' <span id="radio_group_3_'.$value.'" text="'.$label.'">'.$label.'</span></label>';
                        },
                        'class' => 'disabled'
                    ]
                )
                ->label(false);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2 first-col" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Записать', ['id' => 'writedown-button', 'class' => 'btn btn-success', 'style' => 'padding: 3px 2px;  width: 100%;']) ?>
            </div>
        </div>
        <div class="col-sm-1" style="width: 13.5%;">
            <div class="form-group">
                <?= Html::button('Отменить', ['class' => 'btn btn-default', 'style' => 'padding: 3px 4px;', 'data-dismiss' => 'modal', 'aria-hidden' => 'true']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
