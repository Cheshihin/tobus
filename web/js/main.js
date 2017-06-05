 /* js-код используемый и на сайте, и в админке */

 $(document).ready(function() {

     // обновление часов в верхнем меню
     var minutes = new Date().getMinutes();
     setInterval(function() {
         var now = new Date();
         var now_minutes = now.getMinutes();
         if(now_minutes != minutes) {
             minutes = now_minutes;

             // обновим часы
             $.ajax({
                 url: '/site/get-ajax-time',
                 type: 'post',
                 data: {},
                 contentType: false,
                 cache: false,
                 processData: false,
                 success: function (response) {
                     if(response.success == true) {
                         $('#system-time').html(response.time);
                     }else {
                         alert('ошибка');
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
     }, 1000);
 });