var apilist = {};
apilist.login = webHost + '/m/user/login';
apilist.wxlogin = webHost + '/m/user/wxlogin';
apilist.verCodeLogin = webHost + '/m/user/verCodeLogin';
apilist.getPhoneVercode = webHost + '/m/user/getPhoneVercode';
apilist.getAccountPhoneVercode = webHost + '/seller/getAccountPhoneVercode';
apilist.sellerRegister = webHost + '/m/sellerRegister';
apilist.sellerLogin = webHost + '/run/login';
apilist.archSave = webHost +'/seller/lawyer/arch/save';
apilist.orderjiaofuSave = webHost +'/seller/orderjiaofu/save';
apilist.serviceCreateSave = webHost +'/seller/service/create/save';
apilist.answerSave = webHost +'/seller/answer/save';

apilist.orderConfirmSave = webHost +'/run/order/actionSave';
// 请求
function apiRequest(apiname, data, method = 'GET', callback) {
    $.popMsg({
        content: '加载中...',
        closeTime: 0
    });
    $.ajax({
        url: apiname,
        type: method,
        data: data,
        datatype: "json",
        success: function (data) {
            popMsgClose();
            callback(data);
        },
        error: function () {
            popMsgClose();
            alert('请求遇到错误!');
        }
    });
}

function popMsgClose() {
    $('.popup-msg').remove();
    $('.u-popup-box').remove();
    clearInterval(countTimer);
    clearTimeout(closeTimer);
};