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

    if($('#form-tx-opener').size() < 1) {
        var template = '<a id="form-tx-opener" href="#form-tx" class="fb" style="display:none;"></a>';

        $('body').append(template);
    }

    $('[name="jsSendUserDataForm"]').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);
        var data = $this.serializeArray();
        var url = $this.attr('action');
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

        $.ajax({
            url: url,
            type: "post",
            dataType: 'json',
            data: data,
            success: function(obj) {

                if (obj['hasError']) {
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
                alert ('ошибка отправки данных');
                console.log(p1,p2,p3);
            }
        });
    });
});