var $$ = Dom7;
if (document.location.href.includes('safe-areas')) {
    const html = document.documentElement;
    if (html) {
        html.style.setProperty('--f7-safe-area-top', '44px');
        html.style.setProperty('--f7-safe-area-bottom', '34px');
    }
}
if (document.location.href.includes('example-preview')) {
    $('.view-main').attr('data-browser-history', 'true');
    $('.view-main').attr('data-browser-history-root', '/kitchen-sink/core/');
    $('.view-main').attr('data-preload-previous-page', 'false');
    $('.view-main').attr('data-ios-swipe-back', 'false');
    document.documentElement.classList.add('example-preview');
}
var theme = 'ios';
if (document.location.search.indexOf('theme=') >= 0) {
    theme = document.location.search.split('theme=')[1].split('&')[0];
}
if (document.location.search.indexOf('mode=') >= 0) {
    const mode = document.location.search.split('mode=')[1].split('&')[0];
    if (mode === 'dark') document.documentElement.classList.add('dark');
}
var app = new Framework7({
    name:'律鸟-法律咨询',
    el: '#app',
    theme,
    store: store,
    //colors:{yellow: '#ffcc00'},
    //routes:routes,
    clicks: {
        externalLinks: '.external',
    },
    panel: {
        swipe: true,
    },
    view: {
        pushState: true, //像网页一样手机滑动返回上一页
    },
    on: {
        /*routeChange: function(newRoute, previousRoute, router) {
            console.log('开始1');
            this.preloader.show(); //preload here
        },*/
        init: function (e,page) {
            console.log('App initialized');
        },
        pageInit: function (e,page) {
            console.log('Page initialized');
            console.log(e);
            console.log(page);
            console.log(e.name);
            // 用户与律师聊天
            if(e.name == 'user-lawyer-im-chat'){
                var lawyername = $jq('#lawyer_name').val();
                var lawyertoux = $jq('#lawyer_toux').val();
                messagebar = this.messagebar.create({
                    el: '.messagebar',
                    attachments: []
                });
                messages = this.messages.create({
                    el: '.messages',
                    /*messages:[
                        {
                            text: '我是，请在下方输入你的问题，我需要分析案情为你针对性解答。',
                            type: 'received',
                            name: lawyername,
                            avatar: lawyertoux,
                            isTitle:false,
                        }
                    ],*/
                    firstMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!previousMessage || previousMessage.type !== message.type || previousMessage.name !== message.name) return true;
                        return false;
                    },
                    lastMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!nextMessage || nextMessage.type !== message.type || nextMessage.name !== message.name) return true;
                        return false;
                    },
                    tailMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!nextMessage || nextMessage.type !== message.type || nextMessage.name !== message.name) return true;
                        return false;
                    },
                    on: {
                        change: function () {
                            console.log('Textarea value changed');
                        }
                    }
                });

                if(isLogin == 'true'){
                    // 第一次轮询 开始咨询
                    var zixun_start = 0;// 开始咨询
                    /*messages.showTyping({
                        header: lawyername,
                        avatar: lawyertoux,
                    });
                    responseInProgress = true;*/
                    var im_relation_id = $$('#im_relation_id').val();
                    if(im_relation_id == undefined){
                        return false;
                    }
                    msg_query_Interval = setInterval(function(){
                        var im_relation_id = $jq('#im_relation_id').val();
                        var lawyername = $jq('#lawyer_name').val();
                        var lawyertoux = $jq('#lawyer_toux').val();
                        var maxid = $jq('#maxid').val();
                        var formdata = {};
                        formdata.maxid = maxid;
                        formdata.to_type = 2;
                        formdata.zixun_start = zixun_start;
                        formdata.im_type = 'chat';
                        apiRequestF7NotPreloader(webHost + '/m/im/msg-query/'+im_relation_id, formdata, 'POST', function (res) {
                            zixun_start = 1;
                            if (res.status == 'success') {
                                if(res.data.msg_list == ''){
                                    console.log('null');
                                }else{
                                    messages.addMessage({
                                        //textHeader:'咨询付费',
                                        //textFooter:'方便快捷',
                                        //footer:'方便快捷',
                                        text: res.data.msg_list.im_content,
                                        type: 'received',
                                        name: lawyername,
                                        avatar: res.data.msg_list.seller.image,
                                        //image:'',
                                        //imageSrc:'',
                                        //isTitle:true,
                                        //cssClass:'',
                                        attrs: {
                                            'data-id': res.data.msg_list.id,
                                            //'data-author-id': res.data.msg_list.id
                                        }
                                    });
                                    /*if(responseInProgress == true){
                                        messages.hideTyping();
                                        responseInProgress = false;
                                    }*/

                                    if( res.data.msg_list.msg_type == 'seller'){
                                        $jq('#maxid').val(res.data.msg_list.id);
                                    }

                                    /*messages.addMessage({
                                        text: res.data.msg_list.im_content,
                                        type: 'received',
                                        name: res.data.msg_list.seller.lawyer_name,
                                        avatar: res.data.msg_list.seller.image,
                                    });
                                    messages.hideTyping();
                                    responseInProgress = false;
                                    $jq('#maxid').val(res.data.msg_list.id);*/
                                }

                            } else {
                                // 提示
                                app.dialog.alert(res.msg);
                                return false;
                            }
                        });
                    },3000);
                }

            }
            // 用户极速咨询平台
            if(e.name == 'user-fast-imchat'){
                var lawyername = $jq('#lawyer_name').val();
                var lawyertoux = $jq('#lawyer_toux').val();
                messagebar = this.messagebar.create({
                    el: '.messagebar',
                    attachments: [],
                    maxHeight:80,
                });
                messages = this.messages.create({
                    el: '.messages',
                    /*messages:[
                        {
                            text: '我是，请在下方输入你的问题，我需要分析案情为你针对性解答。',
                            type: 'received',
                            name: lawyername,
                            avatar: lawyertoux,
                            isTitle:false,
                        }
                    ],*/
                    firstMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!previousMessage || previousMessage.type !== message.type || previousMessage.name !== message.name) return true;
                        return false;
                    },
                    lastMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!nextMessage || nextMessage.type !== message.type || nextMessage.name !== message.name) return true;
                        return false;
                    },
                    tailMessageRule: function (message, previousMessage, nextMessage) {
                        if (message.isTitle) return false;
                        if (!nextMessage || nextMessage.type !== message.type || nextMessage.name !== message.name) return true;
                        return false;
                    },
                    on: {
                        change: function () {
                            console.log('Textarea value changed');
                        }
                    }
                });

                if(isLogin == 'true'){
                    // 第一次轮询 开始咨询
                    var zixun_start = 0;// 开始咨询
                    /*messages.showTyping({
                        header: lawyername,
                        avatar: lawyertoux,
                    });
                    responseInProgress = true;*/
                    var im_relation_id = $$('#im_relation_id').val();
                    if(im_relation_id == undefined){
                        return false;
                    }
                    msg_query_Interval = setInterval(function(){
                        var im_relation_id = $jq('#im_relation_id').val();
                        var lawyername = $jq('#lawyer_name').val();
                        var lawyertoux = $jq('#lawyer_toux').val();
                        var maxid = $jq('#maxid').val();
                        var formdata = {};
                        formdata.maxid = maxid;
                        formdata.to_type = 2;
                        formdata.zixun_start = zixun_start;
                        formdata.im_type = 'addask';
                        apiRequestF7NotPreloader(webHost + '/m/im/msg-query/'+im_relation_id, formdata, 'POST', function (res) {
                            zixun_start = 1;
                            if (res.status == 'success') {
                                if(res.data.msg_list == ''){
                                    console.log('null');
                                }else{
                                    messages.addMessage({
                                        //textHeader:'咨询付费',
                                        //textFooter:'方便快捷',
                                        //footer:'方便快捷',
                                        text: res.data.msg_list.im_content,
                                        type: 'received',
                                        name: res.data.msg_list.lawyer.lawyer_name,
                                        avatar: res.data.msg_list.lawyer.image,
                                        //image:'',
                                        //imageSrc:'',
                                        //isTitle:true,
                                        //cssClass:'',
                                        attrs: {
                                            'data-id': res.data.msg_list.id,
                                            //'data-author-id': res.data.msg_list.id
                                        }
                                    });
                                    /*if(responseInProgress == true){
                                        messages.hideTyping();
                                        responseInProgress = false;
                                    }*/
                                    if (res.data.msg_list.box_type !== undefined && res.data.msg_list.box_type == 'confirm_addask') {
                                        pay_box();
                                        messages.scroll(500, 10000);
                                        messagebar.setPlaceholder('完成支付后可继续咨询');
                                    }

                                    if( res.data.msg_list.msg_type == 'seller'){
                                        $jq('#maxid').val(res.data.msg_list.id);
                                    }

                                    /*messages.addMessage({
                                        text: res.data.msg_list.im_content,
                                        type: 'received',
                                        name: res.data.msg_list.seller.lawyer_name,
                                        avatar: res.data.msg_list.seller.image,
                                    });
                                    messages.hideTyping();
                                    responseInProgress = false;
                                    $jq('#maxid').val(res.data.msg_list.id);*/
                                    //messages.scroll(500, 10000);
                                }

                            } else {
                                // 提示
                                app.dialog.alert(res.msg);
                                return false;
                            }
                        });
                    },3000);
                }

            }
        },
        routerAjaxSuccess: function(xhr,options) {
            console.log('结束');
            this.preloader.hide(); //preload close
        },
        routerAjaxError: function(xhr,options) {
            console.log('结束2');
            this.preloader.hide(); //preload close
        },
        routerAjaxComplete: function(xhr,options) {
            console.log('结束3');
            this.preloader.hide(); //preload close
        },
        routerAjaxStart: function(xhr,options) {
            console.log('开始');
            this.preloader.show(); //preload here
        },
    },
    cache:false,
    popup: {closeOnEscape: true,},
    sheet: {closeOnEscape: true,},
    popover: {closeOnEscape: true,},
    actions: {closeOnEscape: true,},
    vi: {placementId: 'pltd4o7ibb9rc653x14'},
    dialog: {
        title: '律鸟-法律咨询',
        buttonOk: '确认',
    },
    smartSelect: {
        pageTitle: '',
        //openIn: 'popup',
        pageBackLinkText:'确定',
        popupCloseLinkText:'确定'
    },
    routes: [
        {
            path: '/m/seller-about',
            url: '/m/seller-about',
            options: {
                ignoreCache:true,
            },
        },{
            path: '/m/seller/help/index',
            url: '/m/seller/help/index',
            options: {
                ignoreCache:true,
            },
            on: {
                /*pageAfterIn: function test (e, page) {
                    // do something after page gets into the view
                },*/
                pageInit: function (e, page) {
                    // 下载刷新
                    var ptrContent = $$('.ptr-content');
                    ptrContent.on('ptr:refresh', function (e) {
                        setTimeout(function () {
                            app.ptr.done();
                        }, 2000);
                    });
                    // 上拉加载
                    var loading = false;
                    var lastIndex = $$('.simple-list li').length;
                    // Max items to load
                    var maxItems = 60;
                    var itemsPerLoad = 20;
                    var infinite = $$('.infinite-scroll-content');
                    infinite.on('infinite', function (e) {
                        if (loading) return;
                        loading = true;
                        setTimeout(function () {
                            loading = false;
                            if (lastIndex >= maxItems) {
                                // Nothing more to load, detach infinite scroll events to prevent unnecessary loadings
                                //app.detachInfiniteScroll($$('.infinite-scroll'));
                                // Remove preloader
                                $$('.infinite-scroll-preloader').remove();
                                return false;
                            }
                            var html = '';
                            for (var i = lastIndex + 1; i <= lastIndex + itemsPerLoad; i++) {
                                html += '<li class="item-content"><div class="item-inner"><div class="item-title">Item ' + i + '</div></div></li>';
                            }

                            // Append new items
                            $$('.simple-list ul').append(html);

                            // Update last loaded index
                            lastIndex = $$('.simple-list li').length;
                            //app.ptr.done();
                        }, 2000);
                    });
                },
            }
        },{
            path: '/m/seller/help/detail/:id',
            url: '/m/seller/help/detail/{{id}}',
            options: {
                ignoreCache:true,
            },
        },{
            path: '/m/seller/setting',
            url: '/m/seller/setting',
            options: {
                ignoreCache:true,
            },
        },{
            path: '/m/seller/caller',
            url: '/m/seller/caller',
            options: {
                ignoreCache:true,
            },
        },{
            path: '/m/seller/profile',
            url: '/m/seller/profile',
            options: {
                ignoreCache:true,
            },
        },{
            name:'lawyer_arch',
            path: '/m/seller/lawyer/arch',
            url: '/m/seller/lawyer/arch',
            options: {
                ignoreCache:true,
            },
            on: {
                pageInit: function (e, page) {
                    // 加载地区选择器
                    cityInit('#province_city_district');
                    /*var smartSelect = app.smartSelect.get('.industry-select');
                    smartSelect.on('close',function () {
                        alert('ok');
                    })*/
                    //var $f7 = new Framework7();
                    //alert('ok');
                    var textEditorCustomButtons = app.textEditor.create({
                        el: '.lawyer_desc',
                    });
                },
                pageAfterIn:function (e,page) {
                    var $jq = jQuery.noConflict();
                    var dom1 = {
                        id: "fileup1",// id
                        filter: [uploaderImageFilter],
                        single:true,
                        dir:'',
                        field:'lawyer_image'
                    };
                    var dom2 = {
                        id: "fileup2",// id
                        filter: [uploaderImageFilter],
                        single:true,
                        dir:'',
                        field:'business_license'
                    };
                    setUploader([dom1,dom2]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        },{
            path: '/m/user/lawyer-register',
            url: '/m/user/lawyer-register',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/user/retpass',
            url: '/m/user/retpass',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/order/detail/:id',
            url: '/m/seller/order/detail/{{id}}',
            options: {
                ignoreCache:true,
            },
            on: {

            }
        },{
            path: '/m/seller/orderjiaofu/lists',
            url: '/m/seller/orderjiaofu/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/order/lists',
            url: '/m/seller/order/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/orderjiaofu/detail/:id',
            url: '/m/seller/orderjiaofu/detail/{{id}}',
            options: {
                ignoreCache:true,
            },
            on: {
                pageInit: function (e, page) {
                },
                pageAfterIn:function (e,page) {
                    var $jq = jQuery.noConflict();
                    var dom3= {
                        id: "fileup3",// id
                        filter: [uploaderImageFilter],
                        single:false,
                        dir:'',
                        field:'attach_file[]'
                    };
                    setUploader([dom3]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        },{
            path: '/m/seller/orderjiaofu/lists',
            url: '/m/seller/orderjiaofu/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/service/lists',
            url: '/m/seller/service/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/service/detail/:id',
            url: '/m/seller/service/detail/{{id}}',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/service/create',
            url: '/m/seller/service/create',
            options: {
                ignoreCache:true,
            },
            on:{
                pageInit: function (e, page) {
                    var textEditorCustomButtons = app.textEditor.create({
                        el: '.contents',
                    });
                },
                pageAfterIn:function (e,page) {
                    var $jq = jQuery.noConflict();
                    var dom3= {
                        id: "fileup4",// id
                        filter: [uploaderImageFilter],
                        single:false,
                        dir:'',
                        field:'service_img'
                    };
                    setUploader([dom3]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        },{
            path: '/m/seller/service/create/save',
            url: '/m/seller/service/create/save',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/withdraw/lists',
            url: '/m/seller/withdraw/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/withdraw/apply',
            url: '/m/seller/withdraw/apply',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/withdraw/apply/save',
            url: '/m/seller/withdraw/apply/save',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/finance/lists',
            url: '/m/seller/finance/lists',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/finance/detail/:id',
            url: '/m/seller/finance/detail/{{id}}',
            options: {
                ignoreCache:true,
            }
        },{
            path: '/m/seller/order-ask-detail/:id',
            url: '/m/seller/order-ask-detail/{{id}}',
            options: {
                ignoreCache:true,
            },
            on:{
                pageInit: function (e, page) {
                    var textEditorCustomButtons = app.textEditor.create({
                        el: '.answer_content',
                    });

                    var $jq = jQuery.noConflict();
                    var dom5= {
                        id: "fileup5",// id
                        filter: [uploaderImageFilter],
                        single:false,
                        dir:'',
                        field:'service_img'
                    };
                    setUploader([dom5]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                }
            }
        },{
            path: '/m/seller/order-ask-detail/save',
            url: '/m/seller/order-ask-detail/save',
            options: {
                ignoreCache:true,
            }
        },

    ]
});

var view_main = app.views.get('.view-main');