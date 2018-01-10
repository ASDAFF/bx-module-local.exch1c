$(function () {
    /**
     * если в разметке нет окна благодарности и ссылки его вызывающей, то разместим его
     **/
    if($('#form-tx').size() < 1) {
        var template = '<div id="form-tx" class="popup popup-form" style="display:none;">'
            + '<div class="ftitle">Спасибо!</div>'
            + '<div class="ftext">Данные успешно отправлены</div>'
            + '<button href="javascript:void(0);" class="actionFancyClose">Вернуться на сайт</button>'
            + '</div>';

        $('body').append(template);
    }

    $('.masked-phone').mask('+7(999)999-99-99');

    if($('#form-tx-opener').size() < 1) {
        var template = '<a id="form-tx-opener" href="#form-tx" class="fb" style="display:none;"></a>';

        $('body').append(template);
    }

    $('.jsResetUserDataForm').on('click', function(e) {
        $('.errors-container').html('');
        window.location.reload(true);
    });

    $('.jsDoEditUserDataForm').on('click', function(e) {
        e.preventDefault();
        $('.jsSendUserDataForm').show();
        $('.jsShowUserDataForm').hide();
        $('html, body').animate({ scrollTop: $('#formTopMarker').offset().top }, 'slow');
    });

    $('[name="jsSendUserDataForm"]').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);
        //var data = $this.serializeArray();
        var valid = true;

        $this.find('input[required]').each(function(idx, el){
            var $el = $(el);

            if (!$el.val()) {
                valid = false;
                $el.addClass('error');
            }
        });

        if (!valid) {
            return false;
        }

        var showConfirm = false;
        var editedHtml = '';
        $this.find('.isEdited').each(function(idx, el){
            var $el = $(el);
            var name = $el.parent().find('.input-label').html();

            editedHtml += '<li class="order-popup__list-item"><b>'+name+':</b> '+$el.val()+';</li>';

            showConfirm = true;
        });

        var $container = $('#form-confirm-lk-data');
        var $title = $container.find('.order-popup__text');
        var $list = $container.find('.order-popup__list');
        var $btnConfirm = $container.find('.jsBtnConfirm');
        var $btnClose = $container.find('.jsBtnClose');

        $list.hide();

        $btnConfirm.hide();

        $title.html('Ничего не изменено<br><br><br>');

        if(showConfirm) {
            $title.html('Вы внесли следующие изменения:');
            $list.html(editedHtml);
            $list.show();
            $btnConfirm.show();
        }

        $('#form-confirm-lk-data-opener').trigger('click');

        console.log(editedHtml);
        // открываем окно подтверждения

        // да все ок
        // нет обновим страницу
        return false;


    });

    $('.jsBtnConfirm').on('click', function() {

        var $form = $('[name="jsSendUserDataForm"]');
        var url = $form.attr('action');
        var data = new FormData($form[0]);

        $.ajax({
            url: url,
            type: "post",
            dataType: 'json',
            data: data,

            processData: false,
            contentType: false,
            async: false,
            cache: false,

            success: function(obj) {

                if (obj['hasError']) {
                    $.fancybox.close();
                    $('.errors-container').html(obj['msg']);
                } else {
                    $('.closer-reg').trigger('click');

                    var $formTX = $('#form-tx');
                    var $formTXOpener = $('#form-tx-opener');

                    $formTX.find('.ftext').html(obj['msg']);

                    $.fancybox.close();
                    $formTXOpener.trigger('click');
                }


            },
            error: function(p1,p2,p3) {
                $.fancybox.close();
                alert ('ошибка отправки данных');
                console.log(p1,p2,p3);
            }
        });
    });

    $('.jsEditable').on('change', function() {
        var $this = $(this);
        var oldData = $this.data('oldvalue');
        var newData = $this.val();
        var flagClass = 'isEdited';

        if (newData == oldData) {
            $this.removeClass(flagClass);
            return false;
        }

        $this.addClass(flagClass);

    });
});