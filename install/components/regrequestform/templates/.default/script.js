$(function () {
    $('[name="jsSendFormRegRequest"]').on('submit', function(e) {
        e.preventDefault();

        var $this = $(this);
        var data = $this.serializeArray();
        var url = $this.attr('action');

        $.ajax({
            url: url,
            type: "post",
            data: data,
            success: function(obj) {
                console.log(obj);
            },
            error: function(p1,p2,p3) {
                alert ('ошибка отправки данных');
                console.log(p1,p2,p3);
            }
        });
    });
});