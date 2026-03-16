$('.price_set').on('click',function (e) {
    var days = $(this).attr('data-days');
    var type = $(this).attr('data-type');
    var type_name = $(this).attr('data-type-name');
    var room_id = $(this).attr('data-room_id');
    var room_sku_id = $(this).attr('data-room_sku_id');
    var price = $(this).attr('data-price');
    var open_status = $(this).attr('data-open_status');
    $('#room-sku-price-modal').find('#days').val(days);
    $('#room-sku-price-modal').find('#room_days_time').html(days.replace('days_',''));
    $('#room-sku-price-modal').find('#type').val(type);
    $('#room-sku-price-modal').find('#room_id').val(room_id);
    $('#room-sku-price-modal').find('#room_sku_id').val(room_sku_id);
    $('#room-sku-price-modal').find('#price').val(price);
    $('#room-sku-price-modal').find('input[name="open_status"][value="'+open_status+'"]').prop('checked', true);
    $('#room-sku-price-modal').modal('show');
    //$('#room-sku-price-modal').find('.price-label').html( type_name + '('+ days.replace('days_','')+')');
    $('.price-save').on('click',function (e) {
        var formdata = $('#room-price-form').serializeArray();
        console.log(formdata);
        Dcat.loading();
        var posturl = $('#room-price-form').attr('action');
        $.post({
            url: posturl,
            type:'post',
            data: formdata,
            success: function (response) {
                console.log(response);
                Dcat.loading(false);
                if (! response.status) {
                    Dcat.error(response.data.message);

                    return false;
                }

                Dcat.success(response.data.message);
                //window.location.reload();

                return false;
            }
        });
        return false;
        /*Dcat.Form({
            form: $('#room-price-form'),
            success: function (response) {
                if (! response.status) {
                    Dcat.error(response.data.message);

                    return false;
                }

                Dcat.success(response.data.message);
                Dcat.reload();

                return false;
            },
            error: function () {
                // 非200状态码响应错误
            }
        });*/
    });
    //alert(days + type+ room_id+ price );

    //Dcat.Modal('修改价格');
});