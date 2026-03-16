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
    name: '酒店客房运营',
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
        //iosDynamicNavbar: true,
        xhrCache: true,
        browserHistory: true,
    },
    /*request: { //
        timeout: 5000, // 设置超时为 5000 毫秒，即 5 秒
        error: function (xhr, status) {
            console.log('网络错误');
            app.dialog.alert('网络错误,请检查是否能上网');
        }
    },*/
    on: {
        /*routeChange: function(newRoute, previousRoute, router) {
            console.log('开始1');
            this.preloader.show(); //preload here
        },*/
        init: function (e, page) {
            console.log('App initialized');
        },
        /*pageInit: function (e, page) {
            console.log('Page initialized');
            console.log(e);
            console.log(page);
            console.log(e.name);
        },*/
        routerAjaxStart: function (xhr, options) {
            console.log('开始');
            app.preloader.show(); //preload here
        },
        routerAjaxSuccess: function (xhr, options) {
            console.log('结束');
            this.preloader.hide(); //preload close
        },
        routerAjaxError: function (xhr, textStatus) {
            console.log('结束2');
            if (textStatus === 'timeout') {
                app.dialog.alert('请求超时，请稍后再试。');
            } else {
                app.dialog.alert('请求错误，请稍后再试。');
            }
        },
        routerAjaxComplete: function (xhr, options) {
            console.log('结束3');
            this.preloader.hide(); //preload close
        },

    },
    cache: false,
    popup: {closeOnEscape: true,},
    sheet: {closeOnEscape: true,},
    popover: {closeOnEscape: true,},
    actions: {closeOnEscape: true,},
    vi: {placementId: 'pltd4o7ibb9rc653x14'},
    dialog: {
        title: '酒店客房运营',
        buttonOk: '确认',
        buttonCancel: '取消',
    },
    smartSelect: {
        pageTitle: '',
        //openIn: 'popup',
        pageBackLinkText: '',
        popupCloseLinkText: '确定',
        closeOnSelect: true
    },
    routes: [
        {
            path: '/seller-about',
            url: '/seller-about',
            options: {
                ignoreCache: true,
            },
        }, {
            path: '/seller/help/index',
            url: '/seller/help/index',
            options: {
                ignoreCache: true,
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
        }, {
            path: '/seller/help/detail/:id',
            url: '/seller/help/detail/{{id}}',
            options: {
                ignoreCache: true,
            },
        }, {
            path: '/seller/setting',
            url: '/seller/setting',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    var smartSelect = app.smartSelect.create({
                        el:'.work_status_select',
                        on: {
                            close: function () {
                                var work_status = $$('#work_status').val();
                                var old_status = $$('.work_status_show').attr('data-status');
                                if(work_status == old_status){
                                    return false;
                                }
                                var formdata = {};
                                formdata.field_name = 'work_status';
                                formdata.field_value = work_status;
                                apiRequestF7(webHost+'/seller/infoUp', formdata, 'POST', function (res) {
                                    if (res.status == 'success') {
                                        toastShow(res.msg);
                                        return false;
                                    } else {
                                        // 提示
                                        app.dialog.alert(data.msg);
                                        return false;
                                    }
                                });
                            },
                        }
                    })
                }
            }
        }, {
            path: '/seller/caller',
            url: '/seller/caller',
            options: {
                ignoreCache: true,
            },
        }, {
            path: '/seller/profile',
            url: '/seller/profile',
            options: {
                ignoreCache: true,
            },
        }, {
            name: 'lawyer_arch',
            path: '/seller/lawyer/arch',
            url: '/seller/lawyer/arch',
            options: {
                ignoreCache: true,
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
                pageAfterIn: function (e, page) {
                    var $jq = jQuery.noConflict();
                    var dom1 = {
                        id: "fileup1",// id
                        filter: [uploaderImageFilter],
                        single: true,
                        dir: '',
                        field: 'lawyer_image'
                    };
                    var dom2 = {
                        id: "fileup2",// id
                        filter: [uploaderImageFilter],
                        single: true,
                        dir: '',
                        field: 'business_license'
                    };
                    setUploader([dom1, dom2]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        }, {
            path: '/user/lawyer-register',
            url: '/user/lawyer-register',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/user/retpass',
            url: '/user/retpass',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/order/detail/:id',
            url: '/seller/order/detail/{{id}}',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/orderjiaofu/lists/:id',
            url: '/seller/orderjiaofu/lists/{{id}}',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    $jq('.append-file-list img').on('click', function (e) {
                        var imgpath = $jq(this).attr('src');
                        console.log(imgpath);
                        var photos = [
                            {
                                url: imgpath,
                                caption: '图片预览'
                            },
                        ];
                        const thumbs = [
                            imgpath,
                        ];
                        var standalone = app.photoBrowser.create({
                            photos: photos,
                            //thumbs: thumbs,
                        });
                        standalone.open();
                    });
                    console.log('pageInit');
                },
                pageBeforeOut: function (e, page) {
                    console.log('pageBeforeOut');
                },
                pageAfterOut: function (e, page) {
                    console.log('pageAfterOut');
                }
            }
        }, {
            path: '/seller/order/lists',
            url: '/seller/order/lists',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/orderjiaofu/detail/:id',
            url: '/seller/orderjiaofu/detail/{{id}}',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                },
                pageAfterIn: function (e, page) {
                    var $jq = jQuery.noConflict();
                    var dom3 = {
                        id: "fileup3",// id
                        filter: [uploaderImageFilter],
                        single: false,
                        dir: '',
                        field: 'attach_file[]'
                    };
                    setUploader([dom3]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        }, {
            path: '/seller/service/lists',
            url: '/seller/service/lists',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/service/detail/:id',
            url: '/seller/service/detail/{{id}}',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/service/create',
            url: '/seller/service/create',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    var textEditorCustomButtons = app.textEditor.create({
                        el: '.contents',
                    });
                },
                pageAfterIn: function (e, page) {
                    var $jq = jQuery.noConflict();
                    var dom3 = {
                        id: "fileup4",// id
                        filter: [uploaderImageFilter],
                        single: false,
                        dir: '',
                        field: 'service_img'
                    };
                    setUploader([dom3]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
            }
        }, {
            path: '/seller/service/create/save',
            url: '/seller/service/create/save',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/withdraw/lists',
            url: '/seller/withdraw/lists',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/withdraw/detail/:tixian_no',
            url: '/seller/withdraw/detail/{{tixian_no}}',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/withdraw/apply',
            url: '/seller/withdraw/apply',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/withdraw/binding',
            url: '/seller/withdraw/binding',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/withdraw/binding/form',
            url: '/seller/withdraw/binding/form',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/finance/lists',
            url: '/seller/finance/lists',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/finance/detail/:id',
            url: '/seller/finance/detail/{{id}}',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/order-ask-answer/:qid',
            url: '/seller/order-ask-answer/{{qid}}',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    $jq('.append-file-list img').on('click', function (e) {
                        var imgpath = $jq(this).attr('src');
                        console.log(imgpath);
                        var photos = [
                            {
                                url: imgpath,
                                caption: '图片预览'
                            },
                        ];
                        const thumbs = [
                            imgpath,
                        ];
                        var standalone = app.photoBrowser.create({
                            photos: photos,
                            //thumbs: thumbs,
                        });
                        standalone.open();
                    });
                    console.log('pageInit');
                },
                pageBeforeOut: function (e, page) {
                    console.log('pageBeforeOut');
                },
                pageAfterOut: function (e, page) {
                    console.log('pageAfterOut');
                }
            }
        }, {
            path: '/seller/order-ask-detail/:id',
            url: '/seller/order-ask-detail/{{id}}',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    var textEditorCustomButtons = app.textEditor.create({
                        el: '.answer_content',
                    });

                    var $jq = jQuery.noConflict();
                    var dom5 = {
                        id: "fileup5",// id
                        filter: [uploaderImageFilter],
                        single: false,
                        dir: '',
                        field: 'service_img'
                    };
                    setUploader([dom5]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                }
            }
        }, {
            path: '/seller/order-ask-detail/save',
            url: '/seller/order-ask-detail/save',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/im/lists',
            url: '/seller/im/lists',
            options: {
                ignoreCache: true,
            }
        }, {
            path: '/seller/im/detail/:id',
            url: '/seller/im/detail/{{id}}',
            options: {
                ignoreCache: true,
            },
            on: {
                pageInit: function (e, page) {
                    messagebar = app.messagebar.create({
                        el: '.messagebar',
                        attachments: []
                    });
                    messages = app.messages.create({
                        el: '.messages',
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
                    var im_relation_id = $$('#im_relation_id').val();
                    if (im_relation_id == undefined) {
                        return false;
                    }
                    msg_query_Interval = setInterval(function () {
                        var im_relation_id = $jq('#im_relation_id').val();
                        var maxid = $jq('#maxid').val();

                        var formdata = {};
                        formdata.maxid = maxid;
                        formdata.to_type = 1;
                        apiRequestF7NotPreloader(webHost + '/seller/im/msg/query/' + im_relation_id, formdata, 'POST', function (res) {
                            if (res.status == 'success') {
                                if (res.data.msg_list == '') {
                                    console.log('null');
                                } else {
                                    messages.addMessage({
                                        text: res.data.msg_list.im_content,
                                        type: 'received',
                                        name: res.data.msg_list.user.name,
                                        avatar: res.data.msg_list.user.image_url
                                    });
                                    $jq('#maxid').val(res.data.msg_list.id);
                                }
                            } else {
                                // 提示
                                app.dialog.alert(res.msg);
                                return false;
                            }
                        });
                    }, 3000);

                    var $jq = jQuery.noConflict();
                    var dom5 = {
                        id: "fileup52",// id
                        filter: [uploaderImageFilter],
                        single: false,
                        dir: '',
                        field: 'service_img'
                    };
                    setUploader([dom5]);
                    $jq('.image-close').on('click', function (e) {
                        $jq(this).parent().parent().remove();
                    });
                },
                pageBeforeRemove: function (e, page) {
                    messagebar.destroy();
                    messages.destroy();
                    clearInterval(msg_query_Interval);
                    //clearTimeout(msg_query_Interval);
                    console.log('清除');
                },
            }
        },
        {
            path: '/run/order/lists',
            url: '/run/order/lists',
            options: {
                ignoreCache: true,
            }
        },
        {
            path: '/run/order/detail/:id',
            url: '/run/order/detail/{{id}}',
            options: {
                ignoreCache: true,
            }
        },

    ]
});



var view_main = app.views.get('.view-main');