<link rel="stylesheet" href="{{asset('css/layui.css')}}" media="all">
<link rel="stylesheet" href="/css/menu.css">
<style>
    .fsh-rightPanel {
        background: #fff;
    }

    .weixin-body {
        height: 550px;
    }
</style>
<div class="weixin-menu-setting clear-after" style="min-width: 900px;">
    <div class="mobile-menu-preview">
        <div class="mobile-head-title">公众号</div>
        <ul class="menu-list" id="menu-list">
            <li class="add-item extra" id="add-item">
                <a href="javascript:;" class="menu-link" title="添加菜单"><i
                            class="weixin-icon add-gray"></i></a>
            </li>
        </ul>
    </div>
    <div class="weixin-body">
        <div style="height: 40px;text-align: right;">
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-danger" id="menuSyn">同步到本地</button>
            </div>
        </div>
        <div class="weixin-content-wrap" style="display:none">
            <div class="weixin-content">

                <div class="item-info">
                    <form id="form-item" class="form-item" data-value="">
                        <div class="item-head">
                            <h4 id="current-item-name">添加子菜单</h4>
                            <div class="item-delete"><a href="javascript:;" id="item_delete">删除菜单</a></div>
                        </div>
                        <div style="margin-top: 20px;">
                            <dl>
                                <dt id="current-item-option"><span class="is-sub-item">子</span>菜单标题：</dt>
                                <dd>
                                    <div class="input-box"><input id="item_title" name="item-title"
                                                                  type="text"
                                                                  value=""></div>
                                </dd>
                            </dl>
                            <dl class="is-item">
                                <dt id="current-item-type"><span class="is-sub-item">子</span>菜单内容：</dt>
                                <dd>
                                    <input id="type1" type="radio" name="type" value="click">
                                    <label for="type1" data-editing="1"><span
                                                class="lbl_content">发送消息</span></label>
                                    <input id="type2" type="radio" name="type" value="view">
                                    <label for="type2" data-editing="1"><span
                                                class="lbl_content">跳转网页</span></label>
                                    <input id="type3" type="radio" name="type" value="view_news">
                                    <label for="type3" data-editing="1"><span
                                                class="lbl_content">跳转到图文</span></label>
                                    <input id="type4" type="radio" name="type" value="miniprogram">
                                    <label for="type4" data-editing="1"><span
                                                class="lbl_content">跳转小程序</span></label>
                                    <input id="type5" type="radio" name="type" value="keys">
                                    <label for="type5" data-editing="1"><span
                                                class="lbl_content">点击事件</span></label>

                                    {{--<input id="type3" type="radio" name="type" value="scancode_push"><label
                                            for="type3" data-editing="1"><span
                                                class="lbl_content">扫码推</span></label>
                                    <input id="type4" type="radio" name="type" value="scancode_waitmsg"><label
                                            for="type4" data-editing="1"><span
                                                class="lbl_content">扫码推提示框</span></label>
                                    <input id="type5" type="radio" name="type" value="pic_sysphoto"><label
                                            for="type5" data-editing="1"><span
                                                class="lbl_content">拍照发图</span></label>
                                    <input id="type6" type="radio" name="type" value="pic_photo_or_album"><label
                                            for="type6" data-editing="1"><span
                                                class="lbl_content">拍照相册发图</span></label>
                                    <input id="type7" type="radio" name="type" value="pic_weixin"><label
                                            for="type7" data-editing="1"><span
                                                class="lbl_content">相册发图</span></label>
                                    <input id="type8" type="radio" name="type" value="location_select"><label
                                            for="type8" data-editing="1"><span
                                                class="lbl_content">地理位置选择</span></label>--}}
                                </dd>
                            </dl>
                            <div id="menu-content" class="is-item">
                                <div class="viewbox is-view">
                                    <p class="menu-content-tips">点击该<span class="is-sub-item">子</span>菜单会跳到以下链接
                                    </p>
                                    <dl>
                                        <dt>页面地址：</dt>
                                        <dd>
                                            <div class="input-box"><input type="text" id="url" name="url">
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="viewbox is-keys">
                                    <p class="menu-content-tips">点击该<span class="is-sub-item">子</span>菜单会发送值
                                    </p>
                                    <dl>
                                        <dt>菜单KEY值：</dt>
                                        <dd>
                                            <div class="input-box"><input type="text" id="keyval"
                                                                          name="keyval"></div>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="viewbox is-miniprogram">
                                    <p class="menu-content-tips">点击该<span class="is-sub-item">子</span>菜单会打开小程序
                                    </p>
                                    <dl>
                                        <dt style="width: 130px;">小程序配置信息：</dt>
                                        <dd style="margin-left: 130px;">
                                            <div class="input-box">
                                                <input type="text" id="appconfig" name="appconfig" placeholder="小程序appid,页面路径"></div>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="clickbox is-click" style="display: none;">
                                    <input type="hidden" name="key" id="key" value=""/>
                                    <span class="create-click"><a href="javascript:void(0);"
                                                                  id="select-resources"
                                                                  style="margin-top: 30px;"><i
                                                    class="weixin-icon big-add-gray"></i><strong>选择现有资源</strong></a></span>
                                    <span class="create-click" style="display: none;"><a
                                                href="javascript:void(0);" id="add-resources"><i
                                                    class="weixin-icon big-add-gray"></i><strong>添加新资源</strong></a></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="layui-btn-container" style="margin-top: 50px;text-align: center">
                <button class="layui-btn btn-primary layui-btn-radius" id="menuSave">保存</button>
                <button class="layui-btn btn-primary layui-btn-radius" id="menuPublish">发布</button>
            </div>
        </div>

        <div class="no-weixin-content">
            点击左侧菜单进行编辑操作
        </div>

    </div>
</div>
<script src="{{asset('/js/plugin/Sortable.min.js')}}"></script>
<script>
    var menu = null, responseData = null, responselist = {};


    var init_menu = function (menu) {
        console.log('菜单');
        console.log(menu);
        var str = "";
        var items = menu;
        var type = "", action = "", msgType = "";

        /*$.each(menu,function(i, itemk){
            if (itemk.sub_button != undefined) {
                type = action = "";
            } else {
                type = items[i]['type'];
                if (items[i]['appconfig'] != undefined)
                    action = "appconfig|" + items[i]['appconfig'];
                if (items[i]['keyval'] != undefined)
                    action = "keyval|" + items[i]['keyval'];
                if (items[i]['url'] != undefined)
                    action = "url|" + items[i]['url'];
                if (items[i]['msgId'] != undefined)
                    action = "key|" + items[i]['msgId'];
                if (items[i]['msgType'] != undefined)
                    msgType = items[i]['msgType'];
            }
        });*/

        for (i in items) {
            if (items[i]['sub_button'] != undefined) {
                type = action = "";
            } else {
                type = items[i]['type'];
                if (items[i]['appconfig'] != undefined)
                    action = "appconfig|" + items[i]['appconfig'];
                if (items[i]['keyval'] != undefined)
                    action = "keyval|" + items[i]['keyval'];
                if (items[i]['url'] != undefined)
                    action = "url|" + items[i]['url'];
                if (items[i]['msgId'] != undefined)
                    action = "key|" + items[i]['msgId'];
                if (items[i]['msgType'] != undefined)
                    msgType = items[i]['msgType'];
            }
            str += '<li id="menu-' + i + '" class="menu-item" data-type="' + type + '" data-msgtype="' + msgType + '" data-action="' + action + '" data-name="' + items[i]['name'] + '"> <a href="javascript:;" class="menu-link"> <i class="icon-menu-dot"></i> <i class="weixin-icon sort-gray"></i> <span class="title">' + items[i]['name'] + '</span> </a>';
            var tem = '';
            if (items[i]['sub_button'] != undefined) {
                var sub_menu = items[i]['sub_button'];
                for (j in sub_menu) {
                    type = sub_menu[j]['type'];
                    if (sub_menu[j]['appconfig'] != undefined)
                        action = "appconfig|" + sub_menu[j]['appconfig'];
                    if (sub_menu[j]['keyval'] != undefined)
                        action = "keyval|" + sub_menu[j]['keyval'];
                    if (sub_menu[j]['url'] != undefined)
                        action = "url|" + sub_menu[j]['url'];
                    if (sub_menu[j]['msgId'] != undefined)
                        action = "key|" + sub_menu[j]['msgId'];
                    if (sub_menu[j]['msgType'] != undefined)
                        msgType = sub_menu[j]['msgType'];
                    tem += '<li id="sub-menu-' + j + '" class="sub-menu-item" data-type="' + type + '" data-msgtype="' + msgType + '" data-action="' + action + '" data-name="' + sub_menu[j]['name'] + '"> <a href="javascript:;"> <i class="weixin-icon sort-gray"></i><span class="sub-title">' + sub_menu[j]['name'] + '</span></a> </li>';
                }
            }
            str += '<div class="sub-menu-box" style="' + (i != 0 ? 'display:none;' : '') + '"> <ul class="sub-menu-list">' + tem + '<li class=" add-sub-item"><a href="javascript:;" title="添加子菜单"><span class=" "><i class="weixin-icon add-gray"></i></span></a></li> </ul> <i class="arrow arrow-out"></i> <i class="arrow arrow-in"></i></div>';
            str += '</li>';
        }
        $("#add-item").before(str);
    };
    var refresh_type = function () {
        if ($('input[name=type]:checked').val() == 'view') {
            $(".is-click").hide();
            $(".is-keys").hide();
            $(".is-miniprogram").hide();
            $(".is-view").show();
        } else if ($('input[name=type]:checked').val() == 'click') {
            $(".is-view").hide();
            $(".is-keys").hide();
            $(".is-miniprogram").hide();
            $(".is-click").show();
        } else if ($('input[name=type]:checked').val() == 'view_news') {
            $(".is-view").hide();
            $(".is-keys").hide();
            $(".is-miniprogram").hide();
            $(".is-click").show();
        } else if ($('input[name=type]:checked').val() == 'keys') {
            $(".is-view").hide();
            $(".is-click").hide();
            $(".is-miniprogram").hide();
            $(".is-keys").show();
        } else if ($('input[name=type]:checked').val() == 'miniprogram') {
            $(".is-view").hide();
            $(".is-click").hide();
            $(".is-keys").hide();
            $(".is-miniprogram").show();
        }
    };
    function getWxMenuData() {
        $.ajax({
            url: "{{url('/merchant/wxgzh/getMenuList')}}",
            type: 'post',
            data: {'_token': '{{csrf_token()}}'},
            success: function (result) {
                if (result.code == 200) {
                    menu = result.data && result.data.button ? result.data.button : [];
                    responseData = result.data && result.data.msgs ? result.data.msgs : [];
                    $.each(responseData, function (_, v) {
//                            responselist[v.id]=v.title;
                        responselist[v.id] = {
                            title: v.title,
                            msgType: v.type
                        }
                    });
                    init_menu(menu);
                }
            }

        });
    }
    getWxMenuData();

    //初始化菜单
    //init_menu(menu);
    //拖动排序
    new Sortable($("#menu-list")[0], {draggable: 'li.menu-item'});
    $(".sub-menu-list").each(function () {
        new Sortable(this, {draggable: 'li.sub-menu-item'});
    });
    //添加主菜单
    $("#menu-container").on('click', '#add-item', function () {
        var menu_item_total = $(".menu-item").size();
        if (menu_item_total < 3) {
            var item = '<li class="menu-item" data-type="click" data-action="key|" data-name="添加菜单" > <a href="javascript:;" class="menu-link"> <i class="icon-menu-dot"></i> <i class="weixin-icon sort-gray"></i> <span class="title">添加菜单</span> </a> <div class="sub-menu-box" style=""> <ul class="sub-menu-list"><li class=" add-sub-item"><a href="javascript:;" title="添加子菜单"><span class=" "><i class="weixin-icon add-gray"></i></span></a></li> </ul> <i class="arrow arrow-out"></i> <i class="arrow arrow-in"></i> </div></li>';
            var itemDom = $(item);
            itemDom.insertBefore(this);
            itemDom.trigger("click");
            $(".sub-menu-box", itemDom).show();
            new Sortable($(".sub-menu-list", itemDom)[0], {draggable: 'li.sub-menu-item'});
        }
    });
    $("#menu-container").on('change', 'input[name=type]', function () {
        refresh_type();
    });
    $("#menu-container").on('click', '#item_delete', function () {
        var current = $("#menu-list li.current");
        var prev = current.prev("li[data-type]");
        var next = current.next("li[data-type]");
        var last;
        if (prev.size() == 0 && next.size() == 0 && $(".sub-menu-box", current).size() == 0) {
            last = current.closest(".menu-item");
        } else if (prev.size() > 0 || next.size() > 0) {
            last = prev.size() > 0 ? prev : next;
        } else {
            last = null;
            $(".weixin-content-wrap").hide();
            $(".no-weixin-content").show();
        }
        $("#menu-list li.current").remove();
        if (last) {
            last.trigger('click');
        } else {
            $("input[name='item-title']").val('');
        }
        updateChangeMenu();
    });

    function getMsgByIdAndType(mid, mtype) {
        var d = {};
        $.each(responseData, function (_, v) {
            if (v.id == mid && v.type == mtype) {
                d = v;
                return false;
            }
        });
        return d;
    }

    //更新修改与变动
    var updateChangeMenu = function () {
        var title = $("input[name='item-title']").val();
        var type = $("input[name='type']:checked").val();
        var key = "", value;
        if (type == 'view') {
            key = 'url';
        } else if (type == 'keys') {
            key = 'keyval';
        } else if (type == 'miniprogram') {
            key = 'appconfig';
        } else {
            key = 'key';
        }
        value = $("input[name='" + key + "']").val();
        var msgtype = $("input[name='" + key + "']").attr("data-msgtype");
        if (key == 'key') {
            var msg = getMsgByIdAndType(value, msgtype);
            var keytitle = msg.title ? msg.title : '无标题';
            var cont = $(".is-click .create-click:first");
            $(".keytitle", cont).remove();
            cont.append('<div class="keytitle">资源名:' + keytitle + '</div>');
        }
        var currentItem = $("#menu-list li.current");
        if (currentItem.size() > 0) {
            currentItem.attr('data-type', type);
            currentItem.attr('data-msgtype', msgtype);
            currentItem.attr('data-action', key + "|" + value);
            if (currentItem.siblings().size() == 4) {
                $(".add-sub-item").show();
            } else if (false) {

            }
            currentItem.children("a").find("span").text(title.subByte(0, 16));
            $("input[name='item-title']").val(title);
            currentItem.attr('data-name', title);
            $('#current-item-name').text(title);
        }
        // menuUpdate();
    };
    //更新菜单数据
    var menuUpdate = function () {
        //TODO:操作中保存菜单数据（暂时废弃）
        $.ajax({
            url: "",
            data: {menu: JSON.stringify(getMenuList())},
            success: function (result) {
                if (!result.success) {
                    layer.msg("保存更改异常");
                }
            }
        });
    };
    //获取菜单数据
    var getMenuList = function () {
        var menus = new Array();
        var sub_button = new Array();
        var menu_i = 0;
        var sub_menu_i = 0;
        var item;
        $("#menu-list li").each(function (i) {
            item = $(this);
            var name = item.attr('data-name');
            var type = item.attr('data-type');
            var action = item.attr('data-action');
            var msgType = item.attr('data-msgtype');
            if (name != null) {
                var actions = action.split('|');
                if (item.hasClass('menu-item')) {
                    sub_menu_i = 0;
                    if (item.find('.sub-menu-item').size() > 0) {
                        menus[menu_i] = {"name": name, "sub_button": "sub_button"}
                    } else {
                        if (actions[0] == 'url') {
                            menus[menu_i] = {"name": name, "type": type, "url": actions[1]};
                        } else if (actions[0] == 'keyval') {
                            menus[menu_i] = {"name": name, "type": type, "keyval": actions[1]};
                        } else if (actions[0] == 'appconfig') {
                            menus[menu_i] = {"name": name, "type": type, "appconfig": actions[1]};
                        } else {
                            console.log('123123222');
                            console.log(name + '-' + type);
                            menus[menu_i] = {
                                "name": name,
                                "type": type,
                                "msgId": actions[1],
                                "msgType": msgType
                            };
                        }
                    }
                    if (menu_i > 0) {
                        if (menus[menu_i - 1]['sub_button'] == "sub_button")
                            menus[menu_i - 1]['sub_button'] = sub_button;
                        else
                            menus[menu_i - 1]['sub_button'];
                    }
                    sub_button = new Array();
                    menu_i++;
                } else {
                    if (actions[0] == 'url') {
                        sub_button[sub_menu_i++] = {"name": name, "type": type, "url": actions[1]};
                    } else if (actions[0] == 'keyval') {
                        sub_button[sub_menu_i++] = {"name": name, "type": type, "keyval": actions[1]};
                    } else if (actions[0] == 'appconfig') {
                        sub_button[sub_menu_i++] = {"name": name, "type": type, "appconfig": actions[1]};
                    } else {
                        console.log('333333');
                        console.log(name + '-' + type);
                        console.log(actions);
                        sub_button[sub_menu_i++] = {
                            "name": name,
                            "type": type,
                            "msgId": actions[1],
                            "msgType": msgType
                        };
                    }
                }
            }
        });
        if (sub_button.length > 0) {
            var len = menus.length;
            menus[len - 1]['sub_button'] = sub_button;
        }
        return menus;
    };
    //添加子菜单
    $("#menu-container").on('click', ".add-sub-item", function () {
        var sub_menu_item_total = $(this).parent().find(".sub-menu-item").size();
        if (sub_menu_item_total < 5) {
            var item = '<li class="sub-menu-item" data-type="click" data-action="key|" data-name="添加子菜单"><a href="javascript:;"><span class=" "><i class="weixin-icon sort-gray"></i><span class="sub-title">添加子菜单</span></span></a></li>';
            var itemDom = $(item);
            itemDom.insertBefore(this);
            itemDom.trigger("click");
            if (sub_menu_item_total == 4) {
                $(this).hide();
            }
        }
        return false;
    });
    //主菜单子菜单点击事件
    $("#menu-container").on('click', ".menu-item, .sub-menu-item", function () {
        if ($(this).hasClass("sub-menu-item")) {
            $("#menu-list li").removeClass('current');
            $(".is-item").show();
            $(".is-sub-item").show();
        } else {
            $("#menu-list li").removeClass('current');
            $("#menu-list > li").not(this).find(".sub-menu-box").hide();
            $(".sub-menu-box", this).toggle();
            //如果当前还没有子菜单
            if ($(".sub-menu-item", this).size() == 0) {
                $(".is-item").show();
                $(".is-sub-item").show();
            } else {
                $(".is-item").hide();
                $(".is-sub-item").hide();
            }
        }
        $(this).addClass('current');
        var type = $(this).attr('data-type');
        var action = $(this).attr('data-action');
        var title = $(this).attr('data-name');
        var msgtype = $(this).attr('data-msgtype');

        var actions = action.split('|');
        $("input[name=type][value='" + type + "']").prop("checked", true);
        $("input[name='item-title']").val(title);
        $('#current-item-name').text(title);
        if (actions[0] == 'url') {
            $('input[name=key]').val('');
        } else {
            $('input[name=url]').val('');
        }
        $("input[name='" + actions[0] + "']").val(actions[1]);
        if (actions[0] == 'key') {
            var msg = getMsgByIdAndType(actions[1], msgtype);
            var keytitle = msg.title ? msg.title : '无标题';
            var cont = $(".is-click .create-click:first");
            $(".keytitle", cont).remove();
            if (keytitle) {
                cont.append('<div class="keytitle">资源名:' + keytitle + '</div>');
            }
        } else {

        }

        $(".weixin-content-wrap").show();
        $(".no-weixin-content").hide();

        refresh_type();

        return false;
    });
    $("form#form-item").on('change', "input,textarea", function () {
        updateChangeMenu();
    });
    $("#menu-container").on('click', "#menuSave", function () {
        var menus = getMenuList();
        ajax({
            url: '{{url('/admin/wxgzh/menu/save')}}',
            type: 'post',
            data: {"_token": '{{csrf_token()}}', "menus": JSON.stringify(menus)},
            success: function (result) {
                if (result.status == 'SUCCESS') {
                    showAlert("菜单保存成功！");
                } else {
                    layer.msg("菜单保存失败");
                }
            }
        });
    });
    $("#menu-container").on('click', "#menuPublish", function () {
        var menus = getMenuList();
        ajax({
            url: '{{url('/admin/wxgzh/menu/publish')}}',
            data: {"_token": '{{csrf_token()}}', "menus": JSON.stringify(menus)},
            success: function (result) {
                if (result.status == 'SUCCESS') {
                    showAlert("菜单发布更新成功，生效时间看微信官网说明，或者重新关注微信号！");
                } else {
                    layer.msg("菜单发布失败("+result.msg+")");
                }
            }
        });
    });
    $("#menu-container").on('click', "#menuSyn", function () {
        ajax({
            url: '{{url('/admin/wxgzh/menu/sync')}}',
            data: {"_token": '{{csrf_token()}}'},
            success: function (result) {
                if (result.status == 'SUCCESS') {
                    showAlert("同步成功");
                    window.location.reload();
                } else {
                    layer.msg("同步失败");
                }
            }
        });
    });
    var refreshkey = function (data) {
        $("input[name=key]").val(data.id).attr("data-msgtype", data.type).trigger("change");
        layer.closeAll();
    };

    //选择资源点击
    $("#select-resources").click(function (e) {
        showDialog({
            title: '选择资源',
            template: 'msg',
            height: 500,
            htmlData: {},
            btn: 0,
            success: function () {
                renderSelectTable();
                $("#msgType_select").on("click", ".layui-btn", function () {
                    var $this = $(this);
                    var temp = '<table id="select_table" class="layui-hide" lay-filter="select_table"></table>';
                    $("#send_msg_content").html(template.render(temp, {}));
                    var type = $this.index() == 0 ? "text" : "news";
                    $this.addClass("btn-primary").removeClass("layui-btn-primary")
                        .siblings(".btn-primary").addClass("layui-btn-primary").removeClass("btn-primary");
                    renderSelectTable(type);
                });
            }
        });


        return false;

        var temp = '<div class="fsh-pop">'
            + '<table id="select_table" class="layui-hide" lay-filter="select_table"></table>'
            + '</div>';
        layer.open({
            type: 1,
            title: '选择资源',
            area: ['700px', '500px'],
            content: template.render(temp, {}),
            btn: 0,
            success: function (layero, index) {
                renderSelectTable();
            },
        });
        return false;
    });

    //添加资源点
    //        $(document).on('click', "#add-resources", function () {
    //            Backend.api.open($(this).attr("href") + "?key=" + key, __('Add'), {
    //                callback: refreshkey
    //            });
    //            return false;
    //        });

    //选择资源表格内部操作按钮监听
    /*layui.table.on('tool(select_table)', function (obj) {
        var data = obj.data;
        var layEvent = obj.event;
        if (layEvent === 'choose') {
            refreshkey(data);
        }
    });*/

    function renderSelectTable(type) {
        if (!type) {
            type = $("#msgType_select .btn-primary").index() == 0 ? "text" : "news";
        }
        console.log(type);
        console.log(getresponseDataBytype(type));
        /*var tableObj = layui.table.render({
            id: 'select_table',
            elem: '#select_table',
            data: getresponseDataBytype(type),
            align: "center",
            cols: [[ //表头
                {type: 'numbers'},
                {
                    field: 'type', title: '类型', width: 150, align: 'center', templet: function (d) {
                        return d.type == "text" ? "文本消息" : "图文消息";
                    }
                },
                {field: 'title', title: '标题', width: 250, align: 'center',},
                {field: 'id', title: 'id', width: 80, align: 'center',},
                {
                    field: 'lock', title: '操作', width: 150, align: 'center', templet: function (d) {
                        return '<button class="layui-btn layui-btn-sm " class="font-primary" lay-event="choose"><i class="iconfont icon-add"></i>选择</button>';

                    }, unresize: true, align: 'center'
                }
            ]],
            page: false
        });*/
    }

    function getresponseDataBytype(type) {
        var data = [];
        $.each(responseData, function (_, v) {
            if (v.type == type) {
                data.push(v);
            }
        });
        return data;
    }

</script>
