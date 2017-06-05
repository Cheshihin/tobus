$(function () {
    var tmtb_id = $('#tmtb_id').val();
    //каждые 15сек вызываем обновляем данные на странице
    setInterval(function () {
        if ($('.editable-container').length > 0 || isFocus()) {

        } else {
            $.fn.yiiGridView.update('orders-grid', {
                data: $('#routes-form').serialize()
            });
        }
        $.fn.yiiGridView.update('cars-grid', {
            data: {
                tmtb_id: tmtb_id
            }
        });
    }, 10000);
    return false;
});
//если поле input text  в фокусе то возвращает true
function isFocus() {
    return $("input[type='text']:focus").length > 0;
}
