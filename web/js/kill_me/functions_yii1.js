$(function () {
    var x = '';//счетчик таймера для события элемента #Cars_car_reg

    var container = $(".content"),
        tmtb_from = $('#create_tmtb_form');

    /* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
    /* Written by Andrew Stromnov (stromnov@gmail.com). */
    if ($('.hasDatepicker').length) {
        $.datepicker.regional['ru'] = {
            closeText: 'Закрыть',
            prevText: '&#x3c;Пред',
            nextText: 'След&#x3e;',
            currentText: 'Сегодня',
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
                'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
            dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
            dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
            dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            weekHeader: 'Не',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['ru']);
    }
    $('#main-page').on('click', '.modal_tmtb_client', function () {
        var tmtb_id = $(this).attr('data-tmtb_id'),
            date = $(this).attr('data-date');

        $.ajax({
            type: "POST",
            url: '/clients/ajaxModalTmtb/',
            dataType: 'json',
            data: {'tmtb_id': tmtb_id, 'date': date},
            success: function (response) {
                if (response) {
                    var modal_body = $('#modal_tmtb_client > .modal-body');
                    modal_body.html(response.html);
                    modal_body.find('.form-actions').remove();
                    $('#modal_tmtb_client').modal('show').css({
                        width: '800px',
                        height: 'auto',
                        'margin-left': function () {
                            return -($(this).width() / 2);
                        }
                    });
                }
            }
        });
    });
    //страница /routes ///////////////////////////////////////////////////////////////
    container.on('click', '.car_number', function () {
        var car_id = $(this).attr('rel'),
            tmtb_id = $(this).parents('tr').attr('class');

        $.ajax({
            type: "POST",
            url: '/cars/getCarDriverInfo',
            dataType: 'json',
            data: {'car_id': car_id, 'tmtb_id': tmtb_id},
            success: function (response) {
                var modal_body = $('#modal_car_info > .modal-body');
                if (!response || !response.html) {
                    response.html = 'Извините, данные не найдены!'
                }

                modal_body.html(response.html);
                $('#modal_car_info').modal('show').css({
                    width: '600px',
                    height: 'auto',
                    'margin-left': function () {
                        return -($(this).width() / 2);
                    }
                });
            }
        });
    });

    container.on('click', '.reis_info', function () {
        var ctrl = {
                'modal': $('#modal_reis_info'),
                'body': $('#modal_reis_info > .modal-body')
            },
            $data = {
                'tmtb_id': $(this).attr('rel')
            };

        $.ajax({
            type: "POST",
            url: '/orders/reisInfo',
            dataType: 'json',
            data: $data,
            success: function (response) {
                if (!response || !response.html) {
                    response.html = 'Извините, данные не найдены!'
                }

                ctrl.body.html(response.html);
                ctrl.modal.modal('show').css({
                    width: '600px',
                    height: 'auto',
                    'margin-left': function () {
                        return -($(this).width() / 2);
                    }
                });
            }
        });
    });
    container.on('click', '.tmtb-update', function () {
        var btn = $(this),
            item = {
                "url": btn.data('url')
            },
            ctrl = {
                "modal": $("#modal_update_reis"),
                "modal_body": $("#modal_update_reis .modal-body")
            };
        $.ajax({
            url: item.url,
            type: "POST",
            dataType: "html",
            success: function (res) {
                if (res) {
                    ctrl.modal_body.html(res);
                    ctrl.modal.modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    });
    //если нажали на кнопку изменить в объединенных рейсах
    tmtb_from.on('click', '#edit-merge-data', function () {
        var tmtb2union_id = $(this).attr('data-tmtb2union'),
            route_id = $(this).attr('data-route');

        $.ajax({
            type: "POST",
            url: '/tmtb2union/editMergeData',
            dataType: 'json',
            data: {'id': tmtb2union_id, 'route': route_id},
            success: function (response) {
                if (response.html) {
                    $('.row-data-merge').html(response.html);
                }
            }
        });
    });
    //если нажали кнопку добавить рейс в объединеных рейсах
    tmtb_from.on('click', '#add-merge-data', function () {
        var route_id = $(this).attr('data-route'),
            date = $(this).attr('data-date');

        $.ajax({
            type: "POST",
            url: '/tmtb2union/addMergeData',
            dataType: 'json',
            data: {'route': route_id, 'date': date},
            success: function (response) {
                if (response.html) {
                    $('#add-merge-data').before(response.html);
                }
            }
        });
    });
    // конец скриптов на странице /routes ////////////////////////////////////////////////////


    //меняем значение select #driver_id
    container.on('change', "#car_id", function () {
        var select = $(this),
            data = {
                car_id: $(this).val()
            };
        $.ajax({
            type: "POST",
            url: '/cars/getDriverOnCar',
            dataType: 'json',
            data: data,
            success: function (response) {
                if (!response) {
                    select.parent().siblings('.multiinput-field').children('select').find("option:contains('---')").attr("selected", "selected")
                } else {
                    select.parent().siblings('.multiinput-field').children('select').val(response.driver_id);
                }
            }
        });
    });
//    container.on('click', '.dashed', function(){
//        var tr = $("table tr.multiinput-container:last"),
//            select_car = tr.find('#car_id'),
//            select_driv = tr.find('#driver_id'),
//            all_sel_drivers = $('select[name="driver_id[]"]').serializeArray(),
//            all_sel_cars = $('select[name="car_id[]"]').serializeArray();
//
//        $.each(all_sel_cars, function(key, val){
//            select_car.find("option[value="+val.value+"]").attr('disabled', true);
//        });
//        $.each(all_sel_drivers, function(key, val){
//            select_driv.find("option[value="+val.value+"]").attr('disabled', true);
//        });
//    });
    //при клике на чекбокс появляется кнопка объединить
    container.on('click', '.check_reis', function () {
        $(this).closest('tr');
        $(this).parent('tr').toggleClass('check_border');
        var cheked_reis = $("input[name='merge_array[]']:checked").length,
            btn_merge = $('#btn_merge');

        if (cheked_reis > 1) {
            btn_merge.removeClass('btn_merge_off').addClass('btn_merge_on');
        } else {
            btn_merge.removeClass('btn_merge_on').addClass('btn_merge_off');
        }
    });

    /*Модальное окно добавить рейс */
    container.on("click", '.button_add_reis_modal', function () {
        var btn = $(this),
            item = {
                "url": btn.attr('data'),
                "date": $('#date').val()
            },
            ctrl = {
                "modal": $("#modal_add_reis"),
                "modal_body": $("#modal_add_reis .modal-body")
            };
        $.ajax({
            url: item.url + '?date=' + item.date,
            type: "POST",
            dataType: "html",
            success: function (res) {
                if (res) {
                    ctrl.modal_body.html(res);
                    ctrl.modal.modal('show');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    });
    //если нажали кнопку объединить
    container.on('click', '#btn_merge', function () {
        var array_checked_reis = $("input[name='merge_array[]']:checked"),
            checked_reis = array_checked_reis.serializeArray(),
            url = $(this).attr('data'),
            date = $(this).attr('rel'),
            tmtb_with_one_route = true,
            first_route_id = $("tr[class=" + $("input[name='merge_array[]']:checked").val() + "]").parents("table").attr("data-route");
        // проверяем чтобы рейсы были из одного маршрута
        array_checked_reis.each(function () {
            if (first_route_id != $("tr[class=" + $(this).val() + "]").parents("table").attr("data-route")) {
                tmtb_with_one_route = false;

            }
        });
        //если не из одного выводим алерт
        if (tmtb_with_one_route != true) {
            var html = CreateAlert('', '<strong>Пожалуйста, Выберите рейсы из одного маршрута.', 'error');

            $("#alert").prepend(html);
            $(".alert").fadeIn().delay(3000).queue(function () {
                $(this).remove();
            });
        } else {
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                data: checked_reis,
                success: function (modal_data) {
                    if (modal_data) {
                        var modal_body = $('#modal_merge_reis > .modal-body');
                        modal_body.html(modal_data);
                        $('input[name="date"]').val(date);
                        $('#modal_merge_reis').modal('show').css({
                            width: '600px',
                            height: 'auto',
                            'margin-left': function () {
                                return -($(this).width() / 2);
                            }
                        });
                    }
                }
            });
        }
    });

    $('body').on('click', '.grid-view table input:checkbox', function () {
        setTimeout('countAdminCheckboxes()', 200);
    });


    $("#main-page").on("change", "#Routes_id", function () {
        if ($('#Routes_id option:selected').val() !== '---') {
            var data = {
                route: $("#Routes_id option:selected").val(),
                date: $('#Orders_date').val()
            };
            $.ajax({
                url: "/orders/GetPointsByRoute",
                type: "POST",
                data: data,
                dataType: "json",
                success: function (r) {
                    if (r) {
                        $("#Orders_point_from").html(r.from);
                        $("#Orders_point_to").html(r.to);
                        $("#trip_name").html(r.tmtb);
                        $('.selectpicker').selectpicker('refresh');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });

        }
    });

    $('#c_p_submit').on("click", function () {
            var input_value = true; //если input_value станет false значит есть незаполненные input
            $('.multiinput-field input[name^="name[]"]').filter(function (el) {
                if (!$(this).val().length) {
                    input_value = false
                }
            }).length;

            if ((input_value) &&
                $('.multiinput-field :selected[value="1"]').size() >= 1 &&
                $('.multiinput-field :selected[value="2"]').size() >= 1
                ) {

                $('#cities-form').submit();
            } else {
                CreateAlert($('.myalert'), ' Заполните поля название точки и задайте минимум по одной точке отправления/прибытия', '', false);
            }
            return false;
        }
    );

//    $('#Orders_date').datepicker({'dateFormat':'dd.mm.yy',
//        'showOtherMonths':true,'selectOtherMonths':true,
//        'changeYear':true,'changeMonth':true,'yearRange':'2013:2099',
//        'minDate':'13.01.2014','maxDate':'2099-12-31'});

//    $('#add_client').live("click",function(){
//        $("#Orders_group_order").val('true');
//        $('#order-form').submit();
//});
//    $("#modal_set_status").on("click","#submit_modal_cancel",function(){
//        $.fn.yiiGridView.update('routes-form');
//        console.log(click)
//        return false;
//    });
//
//
    //модальное окно привязки машин к рейсам
    $('#bind_car_modal').on("click", function () {
//        var cid = $('#tmtb_id_value').val();
		var cid = $('#tmtb_id').val();

        ajaxModalBindCar(cid);
    });
    $('.container').on("click", '.add_car_driver', function () {
        var cid = $(this).parents('tr').attr('class');

        ajaxModalBindCar(cid);
    });
    //функция открывает модальное окно с привязкой автомобилей
    function ajaxModalBindCar(cid) {
        $.ajax({
            url: "/tr/openBindCars",
            type: "POST",
            dataType: "json",
            data: {cid: cid},
            success: function (response) {
                if (response) {
                    $('#modal_bind_car .modal-body').html(response);

                    $('#modal_bind_car').modal('show').css({
                        width: '600px',
                        height: 'auto',
                        'margin-left': function () {
                            return -($(this).width() / 2);
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    }

    $("#main-page").on('change', '#Orders_date', function () {
        var routes_id = $('#Routes_id').val();
        //записываем в hidden новую дату
        $("#date").val($(this).val());
        if (routes_id !== '---') {
            $("#trip_name").empty();
            $.ajax({
                url: "/orders/GetTmtbByRoute",
                type: "POST",
                dataType: "json",
                data: {date: $(this).val(),
                    route: routes_id},
                success: function (res) {
                    if (res) {
                        //console.log(res);
                        $("#trip_name").html(res);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }
    });
    /**
     * вылазит предупреждение если не выбрали поле водителя или автомобиль при привязывании машин ajaxModalTmtb
     */
    $('#modal_bind_car').on('click', '#submit_modal_cancel', function () {
        var input_value = true; //если input_value станет false значит есть незаполненные input
        $('.multiinput-field select').filter(function (el) {
            if (!$(this).val().length) {
                input_value = false
            }
        }).length;
        if (!input_value) {
            if (confirm('Незаполненные поля не сохранятся, Вы хотите продолжить?')) {
                $('#cities-form').submit();
            } else {
                return false;
            }
        }
    });
    /*
     * ппроверка номера автомобиля на уникальность(админка/транспорт)
     */
    //$("#Cars_car_reg").on("keypress",function(){
    //    CheckCarReg(x,2000);
    //});
    //$("#Cars_car_reg").on("focusout",function(){
    //    CheckCarReg(x,200);
    //});
    /*вывод уведомления о выборе одинаковых городов (админка/маршруты)*/
    $("#Routes_city_from").on("change", function () {
        if ($(this).val() == $("#Routes_city_to").val()) {
            $("#city_status").remove();
            $("#Routes_city_from").after('<span id="city_status" class="help-inline error">Внимание! Выбраны одинаковые города.</span>');
        } else {
            $("#city_status").remove();
        }
    });
    /*вывод уведомления о выборе одинаковых городов (админка/маршруты)*/
    $("#Routes_city_to").on("change", function () {
        if ($(this).val() == $("#Routes_city_from").val()) {
            $("#city_status").remove();
            $("#Routes_city_to").after('<span id="city_status" class="help-inline error">Внимание! Выбраны одинаковые города.</span>');
            //        $(".form-actions>.btn-primary").css('display','none');
        } else {
            $("#city_status").remove();
            //        $(".form-actions>.btn-primary").css('display','block');
        }
    });
    $("body").on('click', ".free_button", function () {
        var id = $(this).attr('rel');
        if (!$(this).hasClass('disabled')) {
            $(this).addClass('disabled').removeClass("btn-primary");
            ajaxSetFreeOrder(id);
        }
    });
    /*отвязка машины от заказа,при клике на крестике x-editable popup*/
    $("#routes-form").on("click", ".editable-cancel", function () {
        $.ajax({
            url: "/tr/unbindcarfromclient",
            type: "POST",
            dataType: "json",
            data: {id: $(this).parents(".popover").siblings("#car_to_client").attr('data-pk')},
            success: function (res) {
                var tmtb_id = $('#tmtb_id_value').val();
                $.fn.yiiGridView.update('orders-grid', {
                    data: {
                        tmtb_id: tmtb_id
                    }
                });
                $.fn.yiiGridView.update('cars-grid', {
                    data: {
                        tmtb_id: tmtb_id
                    }
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });

    $('body').on("click", '#add_new_order', function () {
        var url = '/orders/create/today';
        var date = $('#date_value').val();
        var tmtb_id = $('#tmtb_id').val();
        ModalRecord(url, date, tmtb_id);

    });

    /*кнопка подтверждения заказа в окне записи клиента*/
    $('#main-page').on('click', '.confirm_btn', function () {
        if (!$('.confirm_btn').attr('disabled')) {
            var d = new Date();
            var hours = d.getHours();
            if (hours < 10)hours = '0' + hours;
            var minutes = d.getMinutes();
            if (minutes < 10)minutes = '0' + minutes;
            console.log(hours + ':' + minutes);
            $('#Orders_time_confirm').val(hours + ':' + minutes);
            $('.confirm_btn').attr('disabled', true);
        }
    });
    /*Чекбокс "Сброс" на странице записи клиента*/
    $('body').on("click", '#Reset_is_reset', function () {
        if ($(this).attr('checked') == 'checked') {
            $('#Orders_reset').prop('disabled', false);
        }
    });

    /*запрет каких-либо действий кнопки Enter в модальном*/
    $('body').on("keypress", '.modal', function () {
        if ((event.keyCode === 13)) {
            return false;
        }
    });

    /*запрет ввода символов в поле число мест, записи клиентов*/
    $('body').on("keypress", '#Orders_places', function (e) {
        return BanKeyPress(e);
    });

    /*запрет ввода символов в поле число мест, записи клиентов*/
    $('body').on("keypress", '.tariff_places', function (e) {
        return BanKeyPress(e);
    });

    /*запрет ввода символов в поле домашний телефон, записи клиентов*/
    $('body').on("keypress", '#Clients_home_phone', function (e) {
        return BanKeyPress(e);
    });

    /*запрет ввода символов в поле домашний телефон, записи клиентов*/
    $('body').on("keypress", '#Orders_fix_price', function (e) {
        return BanKeyPress(e);
    });

    $('body').on("click", '#modal_back_reis .close', function () {
        $('.modal').modal('hide');
    });
    $('body').on("click", '#modal_back_reis #close_b', function () {
        $('.modal').modal('hide');
    });


    /*Модальное окно записи клиентов*/
    $('body').on("click", '.record', function () {

        //var url = $(this).parent('a').attr('href');
        var url = $(this).attr('href');
        ModalRecord(url);
        return false;
    });
    /*изменение поля имя, в записи клиентов*/
    $('body').on("change", '#Clients_name', function () {
        if ($('#Clients_id').val()) {
            if (confirm('Изменить имя только на этот рейс?')) {
                $('#Orders_alt_username').val($(this).val());
            } else {
                $('#Orders_alt_username').val('');
            }
        }
    });
    /*изменение поля имя, в записи клиентов*/
    $('body').on("change", '#Orders_fixed', function () {
        var ctrl = {
            "input" : $("#Orders_fix_price")
        };
        if ($(this).prop("checked")) {
            ctrl.input.attr("disabled", false);
            ctrl.input.val('');
        } else {
            ctrl.input.attr("disabled", true);
            ctrl.input.val();
        }
    });

    $('body').on("change", '#Orders_fix_price', function () {
        var btn = $(this),
            ctrl = {
                "input" : $(".free_order")
            };

        if(btn.val() !== "") {
            ctrl.input.removeClass("show");
        }
    });

    /*Сохранение данных в модальном окне записи*/
    $('body').on("click", '.rec_client', function () {
        if ($(this).hasClass('back_reis')) {
            var is_back = true;
        } else {
            var is_back = false;
        }
        var tmtb_id = $("#trip_name").val(),
            check_end_time = '',
            postornot = true;
        if (tmtb_id)
            check_end_time = checkEndTime(tmtb_id);

        if (check_end_time && check_end_time != 'false') {
            postornot = confirm("Вы уверены? Рейс ушел в " + check_end_time + ".")
        }
        if (postornot) {
            var url = $(this).attr('rel');
            var data = $('.record_client').serialize();
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data,
                success: function (res) {
                    if (res) {
                        if (res.window) {
                            var result = res.window;
                        } else {
                            result = res;
                        }
                        $('#modal_record').html(result);
                        $('#modal_record').modal({
                            backdrop: false,
                            keyboard: true
                        }).css({
                            width: '450px',
                            height: 'auto',
                            top: '0',
                            right: '0',
                            left: 'auto'
                        });

                        if (res.mark == 'true') {
                            setTimeout(function () {
                                $('#modal_record').modal("hide");
                            }, 1000);
                            if (is_back) BackReis(res.client, res.order, res.route);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            return false;
        }
    });

    /*Модальное окно обновления заказа*/
    $('body').on("click", '.upd_order', function () {
        var url = $(this).attr('rel');
        var data = $('.record_client').serialize();
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (res) {
                if (res) {
                    $('#modal_record').html(res.window);
                    if (res.mark == 'true') {
                        $.fn.yiiGridView.update('orders-grid');
                        setTimeout(function () {
                            $('#modal_record').modal("hide");
                        }, 1000);
                    }

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    });
    /*Модальное окно группового заказа*/
    $('body').on("click", '.group_order', function () {
        $("#Orders_group_order").val('true');
        var url = $(this).attr('rel');
        var data = $('.record_client').serialize();
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (res) {
                if (res) {
                    $('#modal_record').html(res);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    });

    $('.logout').on('click', function () {
        if (confirm("Вы действительно хотите выйти?")) {
            window.location = "/site/Logout/";
        }
    });
/////////////
    //если нажали ENTER при наборе номера клиента в поиске на главной
    $("#fnText1").on("keypress", function (e) {
        if (e.which == 13 && $(this).val().length == 15) {
            var date = $("#date").val();
            //window.location = "/site/checkClient/phone/" + $("#fnText1").val() + "/date/" + date;
            $('.modal').modal('hide');
            ajaxClientData('', $("#fnText1").val());
        }
    });

    // Сбрасываем подтверждение в модальном окне
    $('#modal_record').on('change', '#Routes_id, #trip_name, #point_from, #Orders_point_from', function() {
        $('.modal .confirm_time.editable').editable('setValue', null);
        $('#Orders_time_confirm').val("0");
    });
});
//////////////
function countAdminCheckboxes() {
    var count = $('input[type="checkbox"]:checked').length;
    if (count > 0) {
        $('#d_b').css({"display": "block"});
    } else {
        $('#d_b').css({"display": "none"});
    }
}
/**
 * Проверка количества мест, если больше 5 выдать сообщение
 */
function countPlaces(count) {
    var desc = $("#Clients_description"),
        message = 'Требуются дополнительные телефоны заказа';
    if (count > 2) {
        desc.html(message);
    }

    if (desc.val() == message) {
        desc.css({"color": 'red', "font-style": 'italic'});
    }
    if (count > 5) {
        alert('Проверить количество мест заказа!');
    }
}

function changeIsNotPlaces(input)
{
    var btn = $(input),
        ctrl = {
            "places" : $('#Orders_places'),
            "tariff_places" : $('.tariff_places'),
        };
    if(btn.is(":checked")) {
        ctrl.places.val("0").attr("disabled", true);
        ctrl.tariff_places.val("").attr("disabled", true);
    } else {
        ctrl.places.val("").attr("disabled", false);
        ctrl.tariff_places.attr("disabled", false);
    }
}

function checkFreeOrder(places) {
    var item = {
            "form" : $(".record_client"),
            "price" : $("#order_price span.price"),
            "free_order" : $(".free_order"),
            "fix_price" : $("#Orders_fix_price")
        },
        $data = {
            "client_id": $("#Clients_id").val(),
            "places": places,
        };

    $.ajax({
        url:"/orders/bonuses",
        type:"GET",
        dataType: "json",
        data: item.form.serialize(),
        success: function(res) {
            if (res.result && item.fix_price.val() === "") {
                item.free_order.addClass("show");
            } else {
                item.free_order.removeClass("show");
            }

            item.price.html(res.price).addClass("show-inline");
        },
        error:function(jqXHR, textStatus, errorThrown){
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
    return false;



}
/**
 * @obj объект, к которому будет добавляться alert если объект пустой возвращается html
 * @message текст сообщения
 * @type тип сообщения (error, info, warning, success)
 * @pos способ добавления алерта к объекту @obj(true = append, false = prepend)
 */
function CreateAlert(obj, message, type, pos) {
    type = type || 'error';
    switch (type) {
        case'error':
            var str = "<i class='icon-exclamation-sign icon-white'></i><strong>Ошибка!</strong>";
            break;
        case'success':
            var str = "<i class='icon-exclamation-sign icon-white'></i>";
            break;
        case'info':
            var str = "<i class='icon-exclamation-sign icon-white'></i>";
            break;
        case'warning':
            var str = "<i class='icon-exclamation-sign icon-white'></i>";
            break;
        default:
            var str = type;
            break;
    }
    var html = "<div id='my_alert' class='alert in alert-block fade alert-" + type + " my_alert'>\n\
                                         <a class='close' data-dismiss='alert'>×</a>" + str + message + "</div>";
    if (!obj)
        return html;

    obj.html(html);
}


/*
 * Поиск и автозаполнение формы данных клиента в групповом заказе
 */
function FindClient(res) {
    if (res.client) {
//        $("#Clients_id").val("").val(res.id);
//        $("#Clients_home_phone").val("").val(res.home_phone);
//        $("#Clients_name").val("").val(res.name);
//        $("#Clients_alt_phone").val("").val(res.alt_phone);
//        $("#Clients_description").val("").val(res.description);
        $("#Clients_id").val("").val(res.client.id);
        $("#Clients_home_phone").val("").val(res.client.home_phone);
        $("#Clients_name").val("").val(res.client.name);
        $("#Clients_alt_phone").val("").val(res.client.alt_phone);
        $("#Clients_description").val("").val(res.client.description);
    } else {
        $("#Clients_id").val("");
        $("#Clients_home_phone").val("");
        $("#Clients_name").val("");
        $("#Clients_alt_phone").val("");
        $("#Clients_description").val("");
    }
}
/*
 * Автозаполнение полей формы записи клиентов, при вводе номера телефона клиента;
 * @param res - json массив сформированный методом OrdersController::CheckClient();
 */
function InsertClientData(res) {
    if (res) {
        $("#Clients_id").val("").val(res.client.id);
        $("#Clients_home_phone").val("").val(res.client.home_phone);
        $("#Clients_name").val("").val(res.client.name);
        $("#Clients_alt_phone").val("").val(res.client.alt_phone);
        $("#Clients_description").val("").val(res.client.description);
        if (res.order) {
            $("#Orders_point_from").html(res.order.from);
            $("#Orders_point_to").html(res.order.to);
            $("#Orders_point_from").val(res.order.point_from);
            $("#Orders_point_to").val(res.order.point_to);
            $("#Orders_comment").val(res.order.comment);
            $("#Routes_id").val("").val(res.order.route);
            if (!$("#trip_name").val()) {
                $("#trip_name").html(res.order.tmtb);
            }
        }
/*        if (res.free_order) {
            $(".free_order").addClass("show");
        } else {
            $(".free_order").removeClass("show");
        }*/
    } else {
        $("#Clients_id").val("");
        $("#Clients_home_phone").val("");
        $("#Clients_name").val("");
        $("#Clients_alt_phone").val("");
        $("#Clients_description").val("");
    }
}

//Подгружает информацию о клиенте в модальное окно. Используется в /orders/viewOrderByRoute/
function ajaxClientData(client_id, phone) {
    $.ajax({
        url: "/clients/ajaxClientData",
        type: "POST",
        dataType: "json",
        data: {'id': client_id, phone: phone},
        success: function (response) {
            if (response && response.status != 400) {

                $('.modal-body').html(response.html);

                $('#modal_client').modal('show').css({
                    width: '500px',
                    height: 'auto',
                    'margin-left': function () {
                        return -($(this).width() / 2);
                    }
                });
            } else {
                html = CreateAlert('', 'Такого клиента не существует.', 'error');
                $(".myalert").empty().prepend(html);
                $(".alert").fadeIn().delay(3000).fadeOut();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}
function ajaxSetFreeOrder(order_id) {
    $.ajax({
        url: "/orders/setFreeOrder",
        type: "POST",
        dataType: "json",
        data: {'id': order_id},
        success: function (response) {
            var html = '';
            if (response.status == 200) {
                $.fn.yiiGridView.update('orders-grid');
                html = CreateAlert('', 'Данные успешно сохранены!', 'success');
                $(".myalert").prepend(html);
                $(".alert").fadeIn().delay(3000).fadeOut();
            } else {
                CreateAlert($('.myalert'), 'Ошибка при сохранении данных!', 'error');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}
//валидация для времени в таймпикере
function validTimePicker(time) {
    var time = time.split(":");
    if (time[0] > 23)
        time[0] = 23;
    if (time[1] > 59)
        time[1] = 59;

    var get_time = time[0] + ":" + time[1];
    return get_time;
}

// добавляет к времени минуты
function plusToTime(time, plusTimeMinutes) {
    var d = new Date("July 27, 1962 "+ time);
    d.setMinutes(d.getMinutes() + plusTimeMinutes);
    var hours = addNill(d.getHours());
    var minutes = addNill(d.getMinutes());

    return hours+ ':' + minutes;
}

function addNill(a){
    if (a < 10) {
        a = "0" + a;
    }

    return a;
}

function BindCars(block) {
    var last_options = [];
    var last_row = $('.multiinput-container', block).last(),
        clone = last_row.clone();
    last_row.find('option:selected').each(function () {
        last_options.push(this);
    });
    $('.multiinput-container', block).find('option:selected').each(function () {
        last_options.push(this);
    });
    clone.find('option').each(function () {
        for (var i = 0; i < last_options.length; i++) {
            if (this.text === last_options[i].text && this.text !== '---') {
                if (!$(this).siblings().length) {
                    $(this).parent('optgroup').remove();
                }
                this.remove();
            }
        }
    });
    last_row.after(clone);
    $('.multiinput-container', block).last().children('.multiinput-field', block).children('input:text').val('');

}
/* 
 * отвязывает машины от рейса, функция вызывается в файле inputs.php (расширение multitable)
 * @param  elem - кнопка удаления, которая была нажата (this);
 */
function UnbindCars(elem) {
    if ($('.del_row').length > 1) {
        $.ajax({
            url: "/tr/unbindcar",
            type: "GET",
            dataType: "json",
            data: {id: elem.parent('td').siblings().find('#id').val(),
                tmtb: $('#bind_cars_form #tmtb').val(),
                date: $('#bind_cars_form #date').val()},
            success: function (res) {

                alert('Транспортное средство отвязано');
                $.fn.yiiGridView.update("orders-grid");
                $.fn.yiiGridView.update("cars-grid");
                $(".places_count").text(res);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}
/*
 * ппроверка номера мобильного телефона водителя на уникальность(админка/водители)
 */
function CheckDriverPhone() {
    {
        $.ajax({
            url: "/admin/drivers/checkdriver",
            type: "POST",
            dataType: "json",
            data: {phone: $("#Drivers_mobile_phone").val(),
                id: $("#Drivers_id").val()},
            success: function (res) {
                if (res) {
                    $("#Drivers_mobile_phone").removeClass("error").addClass("available");
                    $("#phone_status").remove();
                    $("#Drivers_mobile_phone").after('<span id="phone_status" class="help-inline available">Номер свободен.</span>');
                    $(".form-actions>.btn-primary").css('display', 'block');

                } else {
                    $("#Drivers_mobile_phone").removeClass("available").addClass("error");
                    $("#phone_status").remove();
                    $("#Drivers_mobile_phone").after('<span id="phone_status" class="help-inline error">Номер уже занят другим водителем.</span>');
                    $(".form-actions>.btn-primary").css('display', 'none');
                }
                ;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
        return false;
    }
}
/*НЕ ИСПОЛЬЗУЕТСЯ
 * ппроверка номера автомобиля на уникальность(админка/транспорт)
 * counter - счетчик, глобальная переменная, по умолчанию "";
 * time - время ожидания до выполнения проверки номера во время заполнения пользователем формы 
 */
function CheckCarReg(counter, time) {
    clearTimeout(counter);
    counter = setTimeout(function () {  // заводим таймер
        {
            $.ajax({
                url: "/admin/cars/checkregnumber",
                type: "POST",
                dataType: "json",
                data: {reg: $("#Cars_car_reg").val(),
                    id: $("#Cars_id").val()},
                success: function (res) {
                    if (res) {
                        $("#Cars_car_reg").removeClass("error").addClass("available");
                        $("#phone_status").remove();
                        $("#Cars_car_reg").after('<span id="phone_status" class="help-inline available">Номер свободен.</span>');
                        $(".form-actions>.btn-primary").css('display', 'block');

                    } else {
                        $("#Cars_car_reg").removeClass("available").addClass("error");
                        $("#phone_status").remove();
                        $("#Cars_car_reg").after('<span id="phone_status" class="help-inline error">Номер уже занят другим водителем.</span>');
                        $(".form-actions>.btn-primary").css('display', 'none');
                    }
                    ;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
            return false;
        }
    }, time);
}
function ModalRecord(url, date, tmtb_id) {
    if (!url) {
        url = '/orders/create/day/' + ConvertDayInString(date);
    }
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: {date: date, tmtb_id: tmtb_id},
        success: function (res) {
            if (res) {
                $('#modal_record').html(res);

                $('#modal_record').modal({
                    backdrop: false,
                    keyboard: true
                }).css({
                    width: '450px',
                    height: 'auto',
                    top: '0',
                    right: '0',
                    left: 'auto'
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
    return false;
}
/**
 * @return {string}
 */
function ConvertDayInString(date) {
    var d = new Date(),
        day = d.getFullYear() + '-' + addZero(d.getMonth() + 1) + '-' + addZero(d.getDate());

    if (date == day)
        return 'today';
    else if (date == d.getFullYear() + '-' + addZero(d.getMonth() + 1) + '-' + (d.getDate() + 1))
        return "tomorrow";
    else
        return "another-day";
}
function addZero(i) {
    return (i < 10) ? "0" + i : i;
}
//$('body').on("click",'.back_reis ',function(){
//        setTimeout(function(){
//       
//    });
function BackReis(client, order, route) {
    setTimeout(function () {
        $('body #modal_back_reis').modal({
            backdrop: false,
            keyboard: true
        }).css({
            width: '400px',
            height: 'auto',
            top: '20%',
            right: '0',
            left: 'auto'
        });
    }, 1500);

    /*Модальное окно "Записать обратно"  */
    $('#back_reis').on("click", function () {
        setTimeout(function () {
            var url = '/orders/create/day/another-day';
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: {Bclient: client, Border: order, Broute: route},
                success: function (res) {
                    if (res) {
                        $('#modal_record').html(res);

                        $('#modal_record').modal({
                            backdrop: false,
                            keyboard: true
                        }).css({
                            width: '450px',
                            height: 'auto',
                            top: '0',
                            right: '0',
                            left: 'auto'
                        });
                    }
                }
            });
            return false;
        }, 1000)
    });
}
/*
 * Функция блокирует кнопки клавиатуры, разрешает вводить только цифры,кнопки Backspace и Space
 * @e - событие jQery;
 * Пример: $('body').on("keypress",'#Orders_places',function(e){
 return BanKeyPress(e);
 });
 */
function BanKeyPress(e) {
    var eve = event.keyCode;
    if ((eve > 47) && (eve < 58)) {
        return true;
    }
    switch (eve) {
        case (8):
            return true;
            break;
        case (32):
            return true;
            break;
//        case (45): return true; break;
        default:
            return false;
    }
}
//проверка: end_time рейса последняя точка которого была более 1 часа назад или нет
function checkEndTime(tmtb_id) {
    var result = 'false',
        date = $('#Orders_date').val();
    if (tmtb_id) {
        $.ajax({
            url: "/orders/checkEndTime",
            type: "POST",
            dataType: "json",
            async: false,
            data: {tmtb_id: tmtb_id, date: date},
            success: function (res) {
                result = res.check;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
                console.log(errorThrown);
            }
        });

        return result;
    }
}

function openModal( id, header, body){
    var closeButton = '<button data-dismiss="modal" class="close" type="button">×</button>';

    if(header) {
        $("#" + id + " .modal-header").html( closeButton + '<h3>'+ header + '</h3>');
    }
    $("#" + id + " .modal-body").html(body);
    // $("#" + id + " .modal-footer").html(footer data); // you can also change the footer
    $("#" + id).modal("show");
}