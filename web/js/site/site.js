

$(document).ready(function() {

    $.datepicker.setDefaults($.datepicker.regional["ru"]);
    $('#another-day').datepicker({
        onSelect: function(dateText, inst) {
            location.href = '/?date='+dateText;
        }
    });
});

// функция возвращает дату в формате "dd.mm.yyyy"
function getDate(milliseconds) {

    var date = new Date(milliseconds);

    var today_day = date.getDate();
    if(today_day < 10) {
        today_day = '0' + today_day;
    }

    var today_month = date.getMonth() + 1;
    if(today_month < 10) {
        today_month = '0' + today_month;
    }

    var today_year = date.getFullYear();

    return today_day + '.' + today_month + '.' + today_year;
}


// закрузка модального окна создания заказа (записи пассажира)
function openModalCreateOrder(date, trip_id)
{
    if(void 0 === date) {
        date = '';
    }

    $.ajax({
        url: '/order/ajax-get-form?date=' + date,
        type: 'post',
        data: {},
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if(response.success == true) {

                $('#order-create-modal').find('.modal-body').html(response.html);
                $('#order-create-modal .modal-title').text(response.title);
                $('#order-create-modal').removeClass().addClass('fade modal').addClass(response.class).modal('show');

            }else {
                alert('неустановленная ошибка загрузки формы записи клиента');
            }
        },
        error: function (data, textStatus, jqXHR) {
            if (textStatus == 'error') {
                if (void 0 !== data.responseJSON) {
                    if (data.responseJSON.message.length > 0) {
                        alert(data.responseJSON.message);
                    }
                } else {
                    if (data.responseText.length > 0) {
                        alert(data.responseText);
                    }
                }
            }
        }
    });
}

// функция обновления цены заказа
function updatePrice()
{

    var form = $('#order-client-form');
    var formData = new FormData($('#order-client-form')[0]);

    $.ajax({
        url: '/order/ajax-get-calculate-price',
        type: 'post',
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if(response.success == true) {
                $('#order-client-form #price').text(response.price);
            }else {
                alert('неустановленная ошибка расчета цены');
            }
        },
        error: function (data, textStatus, jqXHR) {
            if (textStatus == 'error') {
                if (void 0 !== data.responseJSON) {
                    if (data.responseJSON.message.length > 0) {
                        alert(data.responseJSON.message);
                    }
                } else {
                    if (data.responseText.length > 0) {
                        alert(data.responseText);
                    }
                }
            }
        }
    });
}


// Запись на сегодня - открытие модального окна создания заказа на сегодня
$('body').on('click', '#new-order-today', function() {
    var now = new Date();
    var now_milliseconds = now.getTime();
    var today_date = getDate(now_milliseconds);
    openModalCreateOrder(today_date);

    return false;
});
$('body').on('click', '#new-order-tomorrow', function() {
    var now = new Date();
    var tomorrow_milliseconds = now.getTime() + 86400000;
    var tomorrow_date = getDate(tomorrow_milliseconds);
    openModalCreateOrder(tomorrow_date);

    return false;
});
$('body').on('click', '#new-order-another-day', function() {
    openModalCreateOrder();

    return false;
});


// во время обновления даты сбрасываются (обнуляются) поля: Направление (и следом должен обнулиться Рейс)
$('body').on('change', '#date', function()
{
    $('#direction').val('').change();
    var date = $.trim($(this).val());

    var radio_group_2_1 = $('#radio_group_2_1').attr('text');
    var radio_group_2_2 = $('#radio_group_2_2').attr('text');
    if(date.length > 0) {//30.05.2017
        var day = parseInt(date.substr(0, 2));
        var month = parseInt(date.substr(3, 2)) - 1;
        var year = parseInt(date.substr(6, 4));

        var yesterdayDate = new Date(year, month, day - 1, 3);
        var day = yesterdayDate.getUTCDate();
        if(day < 10) {
            day = '0' + day;
        }
        var month = yesterdayDate.getUTCMonth() + 1;
        if(month < 10) {
            month = '0' + month;
        }
        var year = yesterdayDate.getUTCFullYear();
        var yesterday_date = day + '.' + month + '.' + year;
        //console.log('yesterday_date=' + yesterday_date);

        radio_group_2_1 = radio_group_2_1.replace('{ДАТА1}', date);
        radio_group_2_2 = radio_group_2_2.replace('{ДАТА2}', yesterday_date);
    }
    $('#radio_group_2_1').text(radio_group_2_1);
    $('#radio_group_2_2').text(radio_group_2_2);

});


// При выборе направления в обновляем список рейсов, обновляем точки "Откуда" и точки "Куда"
$('body').on('change', '#direction', function()
{
    var date = $('#date').val();
    var direction_id = $(this).val();

    // сброс полей "Откуда" и "Куда"
    $('input[name="Order[point_id_from]"]').parents('.sw-element').find('.sw-delete').click();
    $('input[name="Order[point_id_to]"]').parents('.sw-element').find('.sw-delete').click();

    // обновление списка рейсов
    if(direction_id > 0)
    {
        // обновляем рейсы
        $.ajax({
            type: 'POST',
            url: '/trip/ajax-index?date=' + date,
            data: {
                direction_id: direction_id
            }
        }).done(function (trip_list) {

            var options = '';
            options += '<option value="">Выберите рейс</option>';
            for (var key in trip_list) {
                options += '<option value="' + (trip_list[key]).id + '">' + (trip_list[key]).name + '</option>';
            }

            $('#trip').html(options).removeAttr('disabled');
        });

        // обновляем точки "Откуда" и "Куда"
        $.ajax({
            type: 'POST',
            url: '/point/ajax-get-points?direction_id=' + direction_id,
            data: {}
        }).done(function (points_lists)
        {
            var options = '';
            var points_from = points_lists.points_from;
            options += '<option value="">Выберите точку отправления</option>';
            for (var key in points_from) {
                options += '<option value="' + (points_from[key]).id + '">' + (points_from[key]).name + '</option>';
            }

            $('#order-point_id_from').html(options).removeAttr('disabled');

            var options = '';
            var points_to = points_lists.points_to;
            options += '<option value="">Выберите точку отправления</option>';
            for (var key in points_to) {
                options += '<option value="' + (points_to[key]).id + '">' + (points_to[key]).name + '</option>';
            }

            $('#order-point_id_to').html(options).removeAttr('disabled');

            // снимаем блокировку с полей: мобильный, домашний, другой, ФИО
            $('input[name="Client[mobile_phone]"]').removeAttr('disabled');
            $('input[name="Client[home_phone]"]').removeAttr('disabled');
            $('input[name="Client[alt_phone]"]').removeAttr('disabled');
            $('input[name="Client[name]"]').removeAttr('disabled');
        });

    }else {
        var options = '';
        options += '<option value="">---</option>';
        $('#trip').html(options).attr('disabled', true);

        $('input[name="Client[mobile_phone]"]').attr('disabled', true);
        $('input[name="Client[home_phone]"]').attr('disabled', true);
        $('input[name="Client[alt_phone]"]').attr('disabled', true);
        $('input[name="Client[name]"]').attr('disabled', true);
    }
});


// submit формы создания заказа/регистрация клиента
$(document).ready(function()
{
    // чекбокс снятия/установки блокировки для списка информаторских
    $('body').on('change', '#informer-office-disable', function() {
        if($(this).is(':checked')) {
            //console.log('снятие блокировки');
            $('#order-informer_office_id').removeAttr('disabled');
        }else {
            //console.log('установка блокировки');
            $('#order-informer_office_id').val('');
            $('#order-informer_office_id').attr('disabled', true);
        }
    });

    // чекбокс снятия/установки блокировки для фиксированной цены
    $('body').on('change', 'input[name="Order[use_fix_price]"]', function() {
        //if($(this).is(':checked')) {
        //    $('#order-fix_price').removeAttr('disabled');
        //}else {
        //    $('#order-fix_price').val('');
        //    $('#order-fix_price').attr('disabled', true);
        //}

        if($(this).is(':checked')) {
            $('input[name="order-price-disp"]').removeAttr('disabled');
        }else {
            $('input[name="order-price-disp"]').val('');
            $('input[name="order-price-disp"]').attr('disabled', true);
        }
    });

    // чекбокс снятия/установки блокировки для полей: мест и т.п.
    $('body').on('change', '#places-count-disable', function() {
        if($(this).is(':checked')) {
            $('#order-places_count').attr('disabled', true);
            $('#order-student_count').attr('disabled', true);
            $('#order-child_count').attr('disabled', true);
            $('#order-bag_count').attr('disabled', true);
            $('#order-suitcase_count').attr('disabled', true);
            $('#order-oversized_count').attr('disabled', true);
            $('#order-places_count').val('');
            $('#order-student_count').val('');
            $('#order-child_count').val('');
            $('#order-bag_count').val('');
            $('#order-suitcase_count').val('');
            $('#order-oversized_count').val('');

        }else {
            $('#order-places_count').removeAttr('disabled');
            $('#order-student_count').removeAttr('disabled');
            $('#order-child_count').removeAttr('disabled');
            $('#order-bag_count').removeAttr('disabled');
            $('#order-suitcase_count').removeAttr('disabled');
            $('#order-oversized_count').removeAttr('disabled');
        }
    });


    // поиск по номеру телефона существующего клиента
    $('body').on('keyup', '#client-mobile_phone', function()
    {
        var mobile_phone = $(this).val();
        var direction_id = $('#direction').val();

        if(mobile_phone.length == 15 && mobile_phone[mobile_phone.length - 1] != '_' && direction_id > 0) {// считаем что мобильный телефон введен в формате: +7-xxx-xxx-xxxx
            $.ajax({
                url: '/client/ajax-get-client?mobile_phone=' + mobile_phone+'&direction_id='+direction_id,
                type: 'post',
                data: {},
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.success == true) {

                        $('input[name="Client[name]"]').val(data.client.name);
                        $('input[name="Client[home_phone]"]').val(data.client.home_phone);
                        $('input[name="Client[alt_phone]"]').val(data.client.alt_phone);

                        if(data.pointFrom != '') {
                            insertValue($('input[name="Order[point_id_from]"]').parents('.sw-element'), data.pointFrom.id, data.pointFrom.name);

                            if(data.pointFrom.critical_point == 1) {
                                $('input[name="Order[time_air_train_arrival]"]').removeAttr('disabled');
                            }
                        }

                        if(data.pointTo != '') {
                            insertValue($('input[name="Order[point_id_to]"]').parents('.sw-element'), data.pointTo.id, data.pointTo.name);

                            if(data.pointTo.critical_point == 1) {
                                $('input[name="Order[time_air_train_departure]"]').removeAttr('disabled');
                            }
                        }
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    if (textStatus == 'error') {
                        if (void 0 !== data.responseJSON) {
                            if (data.responseJSON.message.length > 0) {
                                alert(data.responseJSON.message);
                            }
                        } else {
                            if (data.responseText.length > 0) {
                                alert(data.responseText);
                            }
                        }
                    }
                }
            });
        }
    });

    // перехват автопроверок полей формы
    //$('body').on('ajaxBeforeSend', '#order-client-form', function(event, jqXHR, textStatus) {
    //    // очищаем ошибки формы
    //    $('#order-client-form').yiiActiveForm('updateMessages', {
    //        //'bonuscardssettings-work_with_service_products': ['Ошибка']
    //    }, true);
    //});
    //$('body').on('ajaxComplete', '#order-client-form', function(event, jqXHR, textStatus) {
    //    var data = jqXHR.responseJSON;
    //    if(data.success == false) {
    //        for(var field in data.order_errors) {
    //            var field_errors = data.order_errors[field];
    //            $('#order-client-form').yiiActiveForm('updateAttribute', 'order-' + field, field_errors);
    //        }
    //
    //        for(var field in data.client_errors) {
    //            var field_errors = data.client_errors[field];
    //            $('#order-client-form').yiiActiveForm('updateAttribute', 'client-' + field, field_errors);
    //        }
    //    }
    //});




    // нажатие на кнопку "Подтвердить"
    $('body').on('click', '#confirm-button', function() {

        var form = $('#order-client-form');
        var formData = new FormData($('#order-client-form')[0]);

        // Так как заблокированные поля форма не отправляет, то "вручную" добавляю их в отправляемые данные
        var data = {};
        $('#order-client-form').find('*[name]:disabled').each(function() {
            var name = $(this).attr('name');
            var value = $(this).attr('value');

            if(value == undefined) {
                value = '';
            }

            // это сделано чтобы для radio-кнопок в массив данных попадало значение только первого radio с именем группы переключателей
            if(data[name] != undefined) {
                data[name] = value;
                formData.append(name, value);
            }
        });

        formData.append('Order[price]', $('input[name="Order[price]"]').val()); // по непонятным причинам в formData это поле не добавилось ранее
        formData.append('submit_button', 'confirm-button');

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data)
            {
                if (data.success == true)
                {
                    //$('#order-client-form').attr('action', data.form_new_action);
                    //$('#confirm-button').val('Подтверждено').removeClass('btn-default').addClass('btn-success').attr('disabled', true);
                    //$('input[name="Order[time_getting_into_car]"]').attr('disabled', true);
                    //$('input[name="Order[radio_group_2]"]').attr('disabled', true);
                    //$('#order-radio_group_2').addClass('disabled');
                    //$('input[name="Order[radio_group_1]"]').removeAttr('disabled');
                    //$('#order-radio_group_1').removeClass('disabled');

                    // обновление формы целиком
                    $('#order-create-modal').find('.modal-body').html(data.form_html);


                }else
                {
                    var errors = '';
                    for(var field in data.client_errors) {
                        var field_errors = data.client_errors[field];
                        for(var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }
                    for(var field in data.order_errors) {
                        var field_errors = data.order_errors[field];
                        for(var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }

                    alert(errors);
                }
            },
            error: function(data, textStatus, jqXHR) {
                if(textStatus == 'error') {
                    if(void 0 !== data.responseJSON) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }
            }
        });
    });


    // нажатие на кнопку "Записать"
    $('body').on('click', '#writedown-button', function() {

        var form = $('#order-client-form');
        var formData = new FormData($('#order-client-form')[0]);

        // Так как заблокированные поля форма не отправляет, то "вручную" добавляю их в отправляемые данные
        var data = {};
        $('#order-client-form').find('*[name]:disabled').each(function() {
            var name = $(this).attr('name');
            var value = $(this).attr('value');

            if(value == undefined) {
                value = '';
            }

            // это сделано чтобы для radio-кнопок в массив данных попадало значение только первого radio с именем группы переключателей
            if(data[name] != undefined) {
                data[name] = value;
                formData.append(name, value);
            }
        });

        formData.append('Order[price]', $('input[name="Order[price]"]').val()); // по непонятным причинам в formData это поле не добавилось ранее
        formData.append('submit_button', 'writedown-button');

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data)
            {
                if (data.success == true)
                {
                    $('#order-create-modal').modal('hide');
                    alert('Успешно сохранено');

                }else
                {
                    var errors = '';
                    for(var field in data.client_errors) {
                        var field_errors = data.client_errors[field];
                        for(var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }
                    for(var field in data.order_errors) {
                        var field_errors = data.order_errors[field];
                        for(var key in field_errors) {
                            errors += field_errors[key] + ' ';
                        }
                    }

                    alert(errors);
                }
            },
            error: function(data, textStatus, jqXHR) {
                if(textStatus == 'error') {
                    if(void 0 !== data.responseJSON) {
                        if(data.responseJSON.message.length > 0) {
                            alert(data.responseJSON.message);
                        }
                    }else {
                        if(data.responseText.length > 0) {
                            alert(data.responseText);
                        }
                    }
                }
            }
        });
    });


    // изменение значения поля "Откуда"
    $(document).on('change', 'input[name="Order[point_id_from]"]', function()
    {
        var point_id_from = $(this).val();

        if(point_id_from > 0) {
            $.ajax({
                url: '/point/ajax-get-point?point_id=' + point_id_from,
                type: 'post',
                data: {},
                contentType: false,
                cache: false,
                processData: false,
                success: function (point) {
                    if (point.critical_point == 1) {
                        $('input[name="Order[time_air_train_arrival]"]').removeAttr('disabled');
                    }else {
                        $('input[name="Order[time_air_train_arrival]"]').attr('disabled', true);
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    if (textStatus == 'error') {
                        if (void 0 !== data.responseJSON) {
                            if (data.responseJSON.message.length > 0) {
                                alert(data.responseJSON.message);
                            }
                        } else {
                            if (data.responseText.length > 0) {
                                alert(data.responseText);
                            }
                        }
                    }
                }
            });
        }else {
            $('input[name="Order[time_air_train_arrival]"]').attr('disabled', true);
        }

        return false;
    });

    // изменение значения поля "Куда"
    $(document).on('change', 'input[name="Order[point_id_to]"]', function()
    {
        var point_id_to = $(this).val();

        if(point_id_to > 0) {
            $.ajax({
                url: '/point/ajax-get-point?point_id=' + point_id_to,
                type: 'post',
                data: {},
                contentType: false,
                cache: false,
                processData: false,
                success: function (point) {
                    if (point.critical_point == 1) {
                        $('input[name="Order[time_air_train_departure]"]').removeAttr('disabled');
                    }else {
                        $('input[name="Order[time_air_train_departure]"]').attr('disabled', true);
                    }
                },
                error: function (data, textStatus, jqXHR) {
                    if (textStatus == 'error') {
                        if (void 0 !== data.responseJSON) {
                            if (data.responseJSON.message.length > 0) {
                                alert(data.responseJSON.message);
                            }
                        } else {
                            if (data.responseText.length > 0) {
                                alert(data.responseText);
                            }
                        }
                    }
                }
            });
        }else {
            $('input[name="Order[time_air_train_departure]"]').attr('disabled', true);
        }

        return false;
    });


    // обновление цены заказа

    // чекбокс "Без места"
    $(document).on('change', 'input[name="Order[is_not_places]"]', function() {
        updatePrice();
    });
    // Мест
    $(document).on('keyup', 'input[name="Order[places_count]"]', function() {
        updatePrice();
    });
    // Студ.
    $(document).on('keyup', 'input[name="Order[student_count]"]', function() {
        updatePrice();
    });
    // Дет.
    $(document).on('keyup', 'input[name="Order[child_count]"]', function() {
        updatePrice();
    });
    //  Сумки
    $(document).on('keyup', 'input[name="Order[bag_count]"]', function() {
        updatePrice();
    });
    //  Чемод.
    $(document).on('keyup', 'input[name="Order[suitcase_count]"]', function() {
        updatePrice();
    });
    // Негабариты
    $(document).on('keyup', 'input[name="Order[oversized_count]"]', function() {
        updatePrice();
    });

    // чекбокс "фикс."
    $(document).on('change', 'input[name="Order[use_fix_price]"]', function() {
        if($(this).is(':checked') == true) {
            var price = $('#order-price-disp').val();
            $('#order-client-form input[name="Order[price]"]').text(price);
        }else {
            updatePrice();
        }
    });
    // Фикс. цена
    $(document).on('keyup', '#order-price-disp', function() {
        var price = $(this).val();
        $('#order-client-form #price').text(price);

        // устранение косяка некоректной работы MaskMoney
        //$('input[name="Order[fix_price]"]').val(price);
    });



    $(document).on('click', '.add_order_plus', function() {
        var trip_id = $(this).attr('trip-id');
        var date = $('#selected-day').attr('date');

        openModalCreateOrder(date, trip_id);
    });

});



