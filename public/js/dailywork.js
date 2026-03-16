// 预定确认
$('.booking-order-confirm').click(function () {
    var order_no = $(this).attr('data-order_no');
    var room_type = $(this).attr('data-room_type');
    var booking_name = $(this).attr('data-booking_name');
    var booking_phone = $(this).attr('data-booking_phone');
    layer.confirm('预定客房：' + room_type + '<br/>预定人：' + booking_name + '<br/>预定人电话：' + booking_phone + '<br/>', {
        icon: 3,
        title: '确认接单',
        btn: ['确认接单', '客房已满 取消订单']
    }, function (index, layero) {
        console.log(index);
        console.log(layero);
        var confirm_type = 1;
        // 更改颜色
        $.ajax({
            method: 'POST',
            url: '/merchant/booking-order-confirm',
            data: {order_no:order_no,confirm_type:confirm_type},
            success: function (res) {
                layer.closeAll();
                console.log(res);
                if(res.code == 200){
                    Dcat.success(res.msg);
                    Dcat.reload();
                }else{
                    Dcat.error(res.msg);

                }
            }
        });
    },function (index, layero) {
        console.log(index);
        console.log(layero);
        var confirm_type = 2;
        // 更改颜色
        $.ajax({
            method: 'POST',
            url: '/merchant/booking-order-confirm',
            data: {order_no:order_no,confirm_type:confirm_type},
            success: function (res) {
                console.log(res);
                if(res.code == 200){
                    Dcat.success(res.msg);
                    layer.closeAll();
                }else{
                    Dcat.error(res.msg);
                    layer.closeAll();
                }
            }
        });
    });
});