/**
 * Created by Михаил on 25.12.13.
 */
$(function () {
    //каждые 15сек вызываем обновляем данные на странице
    setInterval(function () {
        var site_content = $('#site-content'),
            routes_content = $('#routes-content'),
            url = '';

        if (site_content.length) {
            url = '/site/LoadAjax';
        }

        if (routes_content.length) {
            url = '/routes/LoadAjax';
        }

        if (url)
            autoload_ajax(url);
    }, 3000);
});
function autoload_ajax(url) {
    var date = '"' + $('#date').val() + '"';

    $.ajax({
        type: "POST",
        url: url,
        data: {date: date},
        dataType: "json",
        success: function (response) {
            if (response.date)
                $('#date_header').html(response.date);

            if (response.day) {
                $("#yw1").children().removeClass("active_day");
                $("."+response.day).addClass("active_day");
            }


            if (response.time)
                $('#time_header').html(response.time);
            else
                $('#time_header').empty();

            if (response.html) {
                var selectedItems = [];
                $('.check_reis:checked').each(function () {
                    selectedItems.push($(this).val());
                });
                $('.all-routes').html(response.html);

                if (selectedItems.length) {
                    $.each(selectedItems, function (i, val) {
                        $('.check_reis[value="' + val + '"]').attr('checked', 'checked');
                    });
                }
            }
        }
    });
}
