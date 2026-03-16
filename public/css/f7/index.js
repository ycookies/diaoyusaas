notificationFull = app.notification.create({
    icon: '<i class="icon icon-f7"></i>',
    title: '律鸟法律咨询',
    titleRightText: '', // now
    subtitle: '',
    text: 'msg',
    closeTimeout: 3000,
});

function notive(msg) {
    var notificationFull = app.notification.create({
        icon: '',
        title: '酒店客房管理',
        titleRightText: '', // now
        subtitle: msg,
        text: '',
        closeTimeout: 3000,
        closeButton: true
    });
    notificationFull.open();
}

function reloadpage() {
    app.preloader.show();
    view_main.router.refreshPage();
}

function reload() {
    app.preloader.show();
    window.location.reload();
}

// 请求
function apiRequestF7(apiname, data, method = 'GET', callback) {
    app.dialog.preloader();
    var $jq = jQuery.noConflict();
    $jq.ajax({
        url: apiname,
        type: method,
        data: data,
        datatype: "json",
        success: function (data) {
            app.dialog.close();
            callback(data);
        },
        error: function () {
            app.dialog.close();
            app.dialog.alert('请求遇到错误!');
        }
    });
}
// 没有加载提示符的请求
function apiRequestF7NotPreloader(apiname, data, method = 'GET', callback) {
    var $jq = jQuery.noConflict();
    $jq.ajax({
        url: apiname,
        type: method,
        data: data,
        datatype: "json",
        success: function (data) {
            app.dialog.close();
            callback(data);
        },
        error: function () {
            app.dialog.close();
            //app.dialog.alert('请求遇到错误!');
        }
    });
}

function toastShow(title, closeTimeout = 2000, position = 'center') {
    var toastBottom = toastBottom = app.toast.create({
        text: title,
        position: position,
        closeTimeout: closeTimeout,
    });
    toastBottom.open();
}

// 用户登陆
$$('.seller_login').on('click', function () {
    var $jq = jQuery.noConflict();
    var formdata = $jq('#loginForm').serializeArray();
    var username = $jq('#login_username').val();
    var password = $jq('#login_password').val();
    if (username == '') {
        notive('请填写用户名');
        return false;
    }
    if (password == '') {
        notive('请填写密码');
        return false;
    }
    apiRequestF7(apilist.sellerLogin, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            /*app.dialog.alert('登陆成功',function (e) {

            });*/
            toastShow('成功登陆');
            app.loginScreen.close();
            window.location.href = '/run/home';
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
});
// 发送验证码
$$('.send_verfiy_code').on('click', function (e) {
    var phone = $$('#codephone').val();
    var is_agreement = $$('input[name=is_register_agreement]:checked').val();
    if (is_agreement == undefined) {
        toastShow('请仔细阅读相关协议和政策并同意!');
        return false;
    }
    if (phone == '') {
        toastShow('请填写手机号码');
        return false;
    }

    apiRequestF7(apilist.getPhoneVercode, {phone: phone}, 'POST', function (res) {
        if (res.status == 'success') {
            toastShow(res.msg);
            return false;
        } else {
            // 提示
            app.dialog.alert(data.msg);
            return false;
        }
    });
    return false;
});
/* 注册 */
$$('#seller-register-submit').on('click', function (e) {
    var $jq = jQuery.noConflict();
    var is_agreement = $$('input[name=is_register_agreement]:checked').val();
    var phone = $jq('#codephone').val();
    if (is_agreement == undefined) {
        toastShow('请仔细阅读相关协议和政策并同意!');
        return false;
    }
    if (phone == '') {
        toastShow('请填写手机号码');
        return false;
    }
    var formdata = $jq('#lawyerRegForm').serializeArray();
    apiRequestF7(apilist.sellerRegister, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert(res.msg, '', function () {
                app.popup.close('.register-screen');
                window.location.reload();
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            //app.dialog.alert(data.msg);
            return false;
        }
    });
});

function seller_login() {
    var $jq = jQuery.noConflict();
    var formdata = $jq('#loginForm').serializeArray();
    var username = $jq('#login_username').val();
    var password = $jq('#login_password').val();
    if (username == '') {
        notive('请填写用户名');
        return false;
    }
    ;
    if (password == '') {
        notive('请填写密码');
        return false;
    }
    ;
    apiRequestF7(apilist.sellerLogin, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            /*app.dialog.alert('登陆成功',function (e) {

            });*/
            toastShow('成功登陆');
            app.loginScreen.close();
            window.location.href = '/run/home';
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function retpass_login_password() {
    var $jq = jQuery.noConflict();
    var formdata = $jq('#retpassForm').serializeArray();
    var phone = $jq('#retpass_phone').val();
    var verify_code = $jq('#retpass_verify_code').val();
    var new_password = $jq('#retpass_new_password').val();
    var confirm_password = $jq('#confirm_retpass_new_password').val();
    if (phone == '') { toastShow('请填写手机号码'); return false;}
    if (verify_code == '') {toastShow('请填写手机验证码');return false;}
    if (new_password == '') {toastShow('请填写新密码');return false;}
    if (confirm_password == '') {toastShow('请填写确认新密码');return false;}

    apiRequestF7(webHost+'/seller-findpass', formdata, 'POST', function (res) {
        if (res.status == 'success') {
            toastShow('成功登陆');
            app.loginScreen.close();
            window.location.href = '/run/home';
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function order_jiaofu_save() {
    var $jq = jQuery.noConflict();
    var jiaofu_content = $jq('#jiaofu_content').val();
    console.log('4444');
    if (jiaofu_content == '') {
        toastShow('请填写交付说明');
        return false;
    }
    var formdata = $jq('#order-jiaofu-form').serializeArray();
    apiRequestF7(apilist.orderjiaofuSave, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert(res.msg, '', function () {
                view_main.router.back('', {
                    ignoreCache: true,
                    force: true,
                });

            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            //app.dialog.alert(data.msg);
            return false;
        }
    });
}

// 回复保存
function answerSave() {
    var $jq = jQuery.noConflict();
    var formdata = $jq('#question-answer-form').serializeArray();
    var textEditor = app.textEditor.get('.answer_content');
    if (!textEditor.value) {
        toastShow('请填写解答内容');
    }
    formdata.push({name: 'answer_content', value: textEditor.value});
    apiRequestF7(apilist.answerSave, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert('保存成功', function (e) {
                //view_main.router.refreshPage();
                view_main.router.back('', {
                    ignoreCache: true,
                    force: true,
                });
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function archsave1() {
    var textEditor = app.textEditor.get('.lawyer_desc');
    console.log(textEditor);
    /*if (!textEditor.value) {
    }*/
    var $jq = jQuery.noConflict();
    var formdata = $jq('#lawyer-arch-form').serializeArray();
    console.log(formdata);
    //formdata['lawyer_desc'] = textEditor;
    formdata.push({name: 'lawyer_desc', value: textEditor.value});
    apiRequestF7(apilist.archSave, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert('保存成功', function (e) {
                //app.loginScreen.close();
                $jq('.arch_status_txt').html('已认证');
                view_main.router.back();
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function createSave() {
    var textEditor = app.textEditor.get('.contents');
    console.log(textEditor);
    /*if (!textEditor.value) {
    }*/
    var $jq = jQuery.noConflict();
    var formdata = $jq('#service-create-form').serializeArray();
    console.log(formdata);
    formdata.push({name: 'contents', value: textEditor.value});
    apiRequestF7(apilist.serviceCreateSave, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert('保存成功', function (e) {
                $jq('.sold_service_status_txt').html('已设置');
                //app.loginScreen.close();
                view_main.router.back();
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function order_pingjia_save() {
    var $jq = jQuery.noConflict();
    var pingjia_content = $jq('#pingjia_content').val();
    var pingjia_xin = $jq('input[name=pingjia_xin]').val();
    //var ordre_id = $jq('#pingjia_order_id').val();
    if (pingjia_content == '') {
        toastShow('请填写评语');
        return false;
    }
    if (pingjia_xin == '') {
        toastShow('请选择好评星级');
        return false;
    }
    var formdata = $jq('#order-pingjia-form').serializeArray();
    //formdata.push({name:'order_id',value:ordre_id});
    apiRequestF7(webHost + '/order-pingjia-save', formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert('保存成功', function (e) {
                app.popup.close('.service-pingjia');
                view_main.router.refreshPage();
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

// 提交问题补充
function add_ask_append_save() {
    var $jq = jQuery.noConflict();
    var append_content = $jq('#append_content').val();
    var qid = $jq('#question_id').val();
    if (append_content == '') {
        toastShow('请填写补充内容');
        return false;
    }

    var formdata = $jq('#add-ask-append-form').serializeArray();
    formdata.push({name: 'qid', value: qid});
    apiRequestF7(webHost + '/add-ask-append-save', formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert('保存成功', function (e) {
                app.popup.close('.add-ask-append');
                view_main.router.refreshPage();
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

// 申请提现
function withdraw_save() {
    var $jq = jQuery.noConflict();
    var user_balance = $jq('#user_balance').val();
    var tixian_money = $jq('#tixian_money').val();
    var tixian_ways = $jq('input[name=tixian_ways]').val();
    if (user_balance < 10) {
        toastShow('余额不足10元 无法提现');
        return false;
    }
    if (tixian_ways == '') {
        toastShow('请选择提现方式');
        return false;
    }
    if (tixian_money == '') {
        toastShow('请填写提现金额');
        return false;
    }
    var formdata = $jq('#tixian-apply-form').serializeArray();
    apiRequestF7(webHost + '/seller/withdraw/apply/save', formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert(res.msg, function (e) {
                view_main.router.back('', {
                    ignoreCache: true,
                    force: true,
                });
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

// 申请提现账号绑定
function withdraw_binding_save() {
    var $jq = jQuery.noConflict();
    var realname = $jq('#realname').val();
    var alipay_account = $jq('#alipay_account').val();
    var verify_code = $jq('#verify_code').val();
    if (realname == '') {
        toastShow('请填写真实姓名');
        return false;
    }
    if (alipay_account == '') {
        toastShow('请填写支付宝账号');
        return false;
    }
    if (verify_code == '') {
        toastShow('请填写 手机验证码');
        return false;
    }
    var formdata = $jq('#withdraw-binding-form').serializeArray();
    apiRequestF7(webHost + '/seller/withdraw/binding/save', formdata, 'POST', function (res) {
        if (res.status == 'success') {
            app.dialog.alert(res.msg, function (e) {
                view_main.router.back('', {
                    ignoreCache: true,
                    force: true,
                });
            });
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

function send_account_verfiy_code() {
    app.dialog.confirm('将使用本账户绑定的手机获取验证码,请注意查收!', function () {
        apiRequestF7(apilist.getAccountPhoneVercode, {}, 'POST', function (res) {
            if (res.status == 'success') {
                toastShow(res.msg);
                return false;
            } else {
                // 提示
                app.dialog.alert(data.msg);
                return false;
            }
        });
    });

    return false;
}
function checkAttachments() {
    if (messagebar.attachments.length > 0) {
        messagebar.attachmentsShow();
        messagebar.setPlaceholder('给出您解答');
    } else {
        messagebar.attachmentsHide();
        messagebar.setPlaceholder('给出您解答');
    }
}

function sendMessage() {
    var im_relation_id = $jq('#im_relation_id').val();
    var maxid = $jq('#maxid').val();
    var text = messagebar.getValue().replace(/\n/g, '<br />').trim();
    var messagesToSend = [];
    messagebar.attachments.forEach(function (attachment) {
        var size = attachment.split('placeholder/cats-')[1].split('-')[0].split('x');
        messagesToSend.push({
            image: '<img src="' + attachment + '" style="width: ' + (size[0] / 2) + 'px; height: ' + (size[1] / 2) + 'px">'
        });
    });
    if (text.trim().length) {
        messagesToSend.push({
            text: text
        });
    }
    // Reset attachments
    messagebar.attachments = [];
    checkAttachments();
    // Hide sheet
    messagebar.sheetHide();
    // Uncheck selected images in sheet
    messagebar.$sheetEl.find('input').prop('checked', false);
    // Clear area
    messagebar.clear();
    // Focus area
    if (text.length) messagebar.focus();
    // Exit when nothing to send
    if (!messagesToSend.length) return;
    // Send message
    messages.addMessages(messagesToSend);
    var formdata = {};
    formdata.im_content = text;
    apiRequestF7NotPreloader(webHost + '/seller/im/'+im_relation_id+'/sendmsg', formdata, 'POST', function (res) {
        if (res.status == 'success') {

        } else {
            // 提示
            //app.dialog.alert(res.msg);
            return false;
        }
    });

    // Mock response
    if (responseInProgress) return;
    responseInProgress = true;

    /*setTimeout(function () {
        var answer = answers[Math.floor(Math.random() * answers.length)];
        var person = people[Math.floor(Math.random() * people.length)];
        messages.showTyping({
            header: person.name + ' is typing',
            avatar: person.avatar
        });
        setTimeout(function () {
            messages.addMessage({
                text: answer,
                type: 'received',
                name: person.name,
                avatar: person.avatar
            });
            messages.hideTyping();
            responseInProgress = false;
        }, 4000);
    }, 1000);*/
}
function sheetToggle() {
    messagebar.sheetToggle();
}
const images = [
    'https://cdn.framework7.io/placeholder/cats-300x300-1.jpg',
    'https://cdn.framework7.io/placeholder/cats-200x300-2.jpg',
    'https://cdn.framework7.io/placeholder/cats-400x300-3.jpg',
    'https://cdn.framework7.io/placeholder/cats-300x150-4.jpg',
    'https://cdn.framework7.io/placeholder/cats-150x300-5.jpg',
    'https://cdn.framework7.io/placeholder/cats-300x300-6.jpg',
    'https://cdn.framework7.io/placeholder/cats-300x300-7.jpg',
    'https://cdn.framework7.io/placeholder/cats-200x300-8.jpg',
    'https://cdn.framework7.io/placeholder/cats-400x300-9.jpg',
    'https://cdn.framework7.io/placeholder/cats-300x150-10.jpg'
];
const people = [
    {
        name: 'Kate Johnson',
        avatar: 'https://cdn.framework7.io/placeholder/people-100x100-9.jpg'
    },
    {
        name: 'Blue Ninja',
        avatar: 'https://cdn.framework7.io/placeholder/people-100x100-7.jpg'
    },
];
const answers = [
    'Yes!',
    'No',
    'Hm...',
    'I am not sure',
    'And what about you?',
    'May be ;)',
    'Lorem ipsum dolor sit amet, consectetur',
    'What?',
    'Are you sure?',
    'Of course',
    'Need to think about it',
    'Amazing!!!',
];

// 路由定向
function navigateTo(path, xiaoguo) {
    view_main.router.navigate(path, {
        ignoreCache:true,
        transition: xiaoguo
    });
    //router.back(url, options); 返回上一页
    // view_main.router.refreshPage(); // 重载当前页面
    // router.clearPreviousHistory() 清除历史
    // router.updateCurrentUrl(url) 更新当前路由url，并根据传递的url更新router.currentRoute属性（查询、参数、哈希等）。此方法不加载或重新加载任何内容。它只是更改当前的路由url。
    // router.generateUrl({name, query, params})  根据给定的路由名称生成路由url。例如，如果我们采用以下路线：
}

// 打开登陆窗
function loginPage() {
    app.loginScreen.open('.login-screen');
}

function openpop(cals) {
    app.popup.open(cals);
}

function orderConfirmSave() {
    var $jq = jQuery.noConflict();
    var formdata = $jq('#order-confirm-form').serializeArray();
    apiRequestF7(apilist.orderConfirmSave, formdata, 'POST', function (res) {
        if (res.status == 'success') {
            /*app.dialog.alert('登陆成功',function (e) {

            });*/
            toastShow('操作成功');
        } else {
            // 提示
            app.dialog.alert(res.msg);
            return false;
        }
    });
}

