

$(document).ready(function()
{

    // ~~~~~~~~~   страница "Форма редактирования города и точки остановок"    ~~~~~~~~~~~


    // Открытие модального окна 'Добавление точки остановки'
    $(document).on('click', '#points-list #add-point', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Добавление точки остановки');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });

    // Открытие модального окна 'Редактирование точки остановки'
    $(document).on('click', '#points-list .edit-point', function () {

        $('#default-modal').modal('show')
            .find('.modal-body')
            .load(
                $(this).attr('href'),
                {},
                function() {
                    $('#default-modal .modal-title').text('Редактирование точки остановки');
                    $('#default-modal .modal-dialog').css('width', '600px');
                }
            );

        return false;
    });

    // удаление 'Точки остановки'
    $(document).on('click', '#points-list .delete-point', function () {

        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            var city_id = $('#city-form').attr('city-id');
            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    $.pjax.reload({
                        container:"#points-grid",
                        data: {
                            'city_id': city_id
                        }
                    });
                },
                error: function(jqXHR) {
                    alert(jqXHR.responseJSON.message);
                }
            });
        }

        return false;
    });


    $('body').on('submit', '#point-form', function(event) {

        event.preventDefault();
        event.stopImmediatePropagation();


        var form = $(this);
        var formData = $(this).serialize();
        if (form.find('.has-error').length) {
            return false;
        }

        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData,
            success: function (data) {
                // закрытие модального окна
                $('#default-modal').modal('hide');

                // обновление таблицы на странице
                //$.pjax.reload({
                //    container: "#points-grid",
                //    history: false,
                //    type: 'POST',
                //    data: form.serialize(),
                //    url: form.attr('action')
                //});

                $.pjax.reload({
                    container:"#points-grid",
                    data: {
                        'city_id': data.city_id
                    }
                });

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



    // ~~~~~~~~~   страница "список Городов"    ~~~~~~~~~~~


    // перехват события удаления города
    $(document).on('click', '#city-page .delete-city', function () {
        if(confirm('Вы уверены, что хотите удалить этот элемент?')) {

            $.ajax({
                type: "POST",
                url: $(this).attr('href'),
                data: {},
                success: function(data, textStatus, jqXHR)
                {
                    location.reload();
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
        }

        return false;
    });

});