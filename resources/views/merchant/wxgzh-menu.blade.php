
<link rel="stylesheet" href="/css/wx_menu.css">
<div class="fluid">
    <div class="pannel" id="menu-box">
        <div class="wx-card">
            <div class="wx-box">
                <div class="wx-head">{{$oauth->app_name}}</div>
                <div class="wx-body">
                    <p class="content-tips">内容区域</p>
                    <div class="wx-footer">
                        <li class="add-menu">
                            <i class="fa fa-spinner" id="menu_loading"></i>
                        </li>
                    </div>
                </div>
            </div>
            <div class="wx-editor">
                <a href="javascript:;" class="editor-load"><i class="fa fa-spinner"></i></a>
                <span class="editor-tips">请点击左侧菜单</span>
                <div class="wx-editor-show">
                    <div class="wx-editor-head">菜单一<a class="menu-delete">删除该菜单</a></div>
                    <div class="wx-editor-body">

                        <div class="cjb-form-group">
                            <label for="">菜单名称:</label>
                            <div class="cjb-input-block">
                                <input type="text" class="cjb-input" name="cjb-name" placeholder="菜单名称">
                            </div>
                        </div>
                        {{--<input id="type1" type="radio" name="type" value="click">
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
                                    class="lbl_content">点击事件</span></label>--}}
                        <div class="cjb-form-group cjb-radio-group">
                            <label for="">菜单类型:</label>
                            <div class="cjb-input-block">
                                <li class="cjb-radio select" type="text">发送消息</li>
                                <li class="cjb-radio select" type="view">跳转链接</li>
                                <li class="cjb-radio select" type="miniprogram">跳转小程序</li>
                            </div>
                        </div>
                        <style>
                            .cjb_miniprogram_appid{
                                display: none;
                            }
                        </style>
                        <div class="cjb-form-group cjb-val-group cjb_miniprogram_appid">
                            <label for="">小程序Appid:</label>
                            <div class="cjb-input-block">
                                <input type="text" class="cjb-input " name="miniprogram_appid" placeholder="" value="{{$miniprogram_appid}}">
                            </div>
                        </div>
                        <div class="cjb-form-group cjb-val-group">
                            <label for="">菜单值:</label>
                            <div class="cjb-input-block">
                                <input type="text" class="cjb-input" name="cjb-val" placeholder="菜单包含的值">
                                <p class="cjb-input-tips">请输入包含http://或者https://的完整链接</p>
                            </div>
                        </div>
                        <div class="cjb-form-group">
                            <div class="cjb-input-block">
                                <button  type="button" class="btn btn-success set_menu">确认修改</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div style="text-align: center;margin-top: 20px">
            <input type="hidden" name="miniprogram_appid" id="miniprogram_appid" value="{{$miniprogram_appid}}">
            <button type="button" class="btn btn-success create_menu">保存</button>

        </div>
        {{--<span style="font-size: 12px;">得到的json数据：</span>
        <pre id="json"></pre>--}}
    </div>
</div>
<script src="js/wx_menu.js?v={{time()}}"></script>
<script>
    $.ajax({
        url: '{{url('/merchant/wxgzh/getMenuList')}}',
        type: 'get',
        async: false, // 加载完再执行，很重要
        dataType: 'json',
        success: function(res) {
            //var data = JSON.parse(res);
            setMenuList(res.data);
            setTimeout(function () {
                $('#menu_loading').removeClass('fa-spinner');
                $('#menu_loading').addClass('fa-plus');
            },800)

        }
    })
    $('#menu-box').off().on('click','.menu-item .menu-item-label',function () {
        let t = $(this);
        if (!t.parent().hasClass('ac')) {
            $('.menu-item').removeClass('ac');
            t.parent().addClass('ac');
        }

        $('.child-menu-item').removeClass('active');
        // 呼出编辑器 假装动态加载（‘—’）！！！
        _load(function () {
            $('.wx-editor-show').show(),$('.editor-tips').hide();
        },200)

        setFormVal(getMenuVal(t),1);
        if (t.parent().find('.child-menu-item').length > 0) {
            delMenuVal(t);
        } else {
            $('.cjb-radio-group').show();
            $('.cjb-val-group').show();
            $('.cjb_miniprogram_appid').hide();
        }
        ot = t;
    })
    // 二级菜单点击事件
    $('#menu-box').on('click','.child-menu-item .child-menu-item-label',function () {
        let t = $(this);
        if (!t.parent().hasClass('active')) {
            $('.child-menu-item').removeClass('active');
            t.parent().addClass('active');
        }
        // 执行输入框显示
        $('.cjb-radio-group').show();
        $('.cjb-val-group').show();
        $('.cjb_miniprogram_appid').hide();
        // 呼出编辑器 假装动态加载（‘—’）！！！
        _load(function () {
            $('.wx-editor-show').show(),$('.editor-tips').hide();
        },200)
        setFormVal(getMenuVal(t),2);
        ot = t;
    })
    Dcat.ready(function () {
        // 写你的逻辑
        //
        //一级菜单点击事件
        /**/
    });
</script>
