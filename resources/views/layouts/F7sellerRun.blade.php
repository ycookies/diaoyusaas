<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#fff">
    <meta http-equiv="Content-Security-Policy" content="default-src * 'self' 'unsafe-inline' 'unsafe-eval' data:">
    <title>{{config('app.name')}} | @yield('title') </title>
    <link rel="stylesheet" href="{{asset('css/f7/framework7-bundle81.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/jquery.magnify.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/f7/app.css')}}">
    <link rel="stylesheet" href="{{asset('oss/dist/css/plupload.css')}}">
    {{--<script type="text/javascript" src="{{asset('tinymce/tinymce.min.js')}}" referrerpolicy="origin"></script>
    --}}

    <style>
        i.icon.icon-back{
            width: 25px;
            background-image: url("{{asset('images/back1.svg')}}");
        }
        .ios .icon-back:after{
            content:'';
        }
        .ios .navbar-inner {
            box-shadow: 0 1px 6px #ccc;
        }
        :root{
            --f7-label-font-size:14px;
        }

        .float-clear {
            clear: both;
        }

        .block-title {
            margin-top: 10px;
        }

        .block, .list {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .grid-cols-3 {
            text-align: center;
        }

        .box-num, .box-title {
            font-size: 18px;
            color: #cccccc;
        }

        #lawyer-arch-form .item-label {
            color: #b5b5b5;
        }

        .right-lox {
            float: right;
        }

        .right-lox a {
            float: right;
        }

        .login-bg {
            background: #fff url('{{asset('css/pic/bg.png')}}') no-repeat !important;
            background-size: 100% auto !important;
        }

        .top-banner img {
            display: block;
            width: 100%;
        }

        .ban-tx {
            position: absolute;
            left: 0;
            top: 0;
            height: 10.8rem;
            padding: 3.25rem 1.2rem 0;
            color: #fff;
        }

        .tit {
            font-size: 1.8rem;
            line-height: 2.5rem;
            font-weight: 700;
        }

        .fg {
            font-size: .9rem;
            line-height: 1.2rem;
            opacity: .85;
            margin-top: 0.4rem;
        }
        .upimg {
            width: 100px;
            height: 100px;
            background-color: #1a2226;
            float: none;
            margin-left: 10px;
        }
        .imglist,.nongc{
            margin: 3px;
        }
        .imglist ul {
            list-style: none;
            margin-left: 15px;
            padding: 0px;
        }
        .imglist img{
            width: 50px;
            max-height:50px;
        }
        .imglist a{
            position: absolute;
            right: 2px;
            top:-8px;
            font-size: 10px;
            z-index: 100;
        }
        .nongc ul {
            list-style: none;
            margin-left: 15px;
            padding: 0px;
        }
        .nongc li{
            float: left;
            margin: 0px;
        }
        .imglist li {
            position: relative;
            float: left;
            margin: 5px;
            width: 50px;
            height: 50px;
            /*overflow: hidden;*/
            border: 1px solid #cccccc;
        }
        .upfiles{
            border: 0px !important;
        }
        .div1 {
            position: relative;
            float: left;
        }

        .div2 {
            width: 50px;
            height: 50px;
            color: #fff;
            text-align: center;
            line-height: 50px;
            margin: 0px;
            background-image: url("{{asset('images/iconfont-tianjia.png')}}");
            background-repeat:no-repeat;
            background-position:left;
            background-size:100% 100%;
        }

        .file_input {
            width: 50px; /*因为file-input在部分浏览器中会自带一个输入框，需要双击才可以点击上传,放大后将其定位到div外面就好啦*/
            height: 36px;
            position: absolute;
            top: 0;
            z-index: 1;
            -moz-opacity: 0;
            -ms-opacity: 0;
            -webkit-opacity: 0;
            opacity: 0; /*css属性——opcity不透明度，取值0-1*/
            filter: alpha(opacity=0); /*兼容IE8及以下--filter属性是IE特有的，它还有很多其它滤镜效果，而filter: alpha(opacity=0); 兼容IE8及以下的IE浏览器(如果你的电脑IE是8以下的版本，使用某些效果是可能会有一个允许ActiveX的提示,注意点一下就ok啦)*/
            cursor: pointer;
        }
        .mui-checkbox label{
            padding-right: 50px;

        }
        .nongc label{
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .ditu{
            display: inline-block;
            width: 30px;
            height: 30px;
            margin-top: 8px;
            background-color: #ffffff;
            background-image: url("{{asset('images/ditu.png')}}");
            background-repeat:no-repeat;
            background-position:left;
        }
        .images_zone{
            width: 120px;
            height: 130px;
            border: 1px solid #dddddd;
            text-align: center;
            margin-top: 10px;
            margin-right: 10px;
            display: inline-block;
            position: relative;
        }
        .images_zone img{
            width: 100px;
            max-height: 100px;
        }
        .images_zone a{
            position: absolute;
            right: 0px;
            top:0px;
            left: auto;
            display: block;
            width: auto;
            margin-top: -2px;
            background:transparent;
        }
        .up-list .image-close {
            position: absolute;
            display: inline-block;
            right: -6px;
            top: -6px;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            border-radius: 12px;
            background-color: #FF5053;
            color: #f3f3f3;
            border: solid 1px #FF5053;
            font-size: 9px;
            font-weight: 200;
            z-index: 1;
        }
        .progress{
            width: 100%;
            height: 13px;
            font-size: 10px;
            line-height: 14px;
            overflow: hidden;
            background: #adadad;
            margin: 5px 0;
        }
        .progress .progress-bar{
            height: 13px;
            background: #11ae6f;
            float: left;
            color: #fff;
            text-align: center;
            width: 0%;
        }
        .tou-img{
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }
        .imglist .img-box{
            margin: 5px;
            display: inline-block;
            padding: 2px;
            border: 1px solid #cccccc;
            width: 50px;
            height: 50px;
            overflow: hidden;
        }
        .font12{font-size:12px;}
        .font12 i{font-size:16px;}
        .font14{font-size:14px;}
        .font14 i{font-size:18px;}
        .font16{font-size:16px;}
        .font16 i{font-size:20px;}
        .font18{font-size: 18px;}
        .font18 i{font-size: 22px;}
        .font20{font-size: 20px;}
        .font20 i{font-size: 24px;}
        .text-color-grey,.text-color-grey i{color: #868EA3;}
        .text-color-grey1,.text-color-grey1 i{color: #111f34;}
        .text-color-grey2,.text-color-grey2 i{color: #cccccc;}
    </style>
    <script>
        @if(env('ADMIN_HTTPS'))
        var webHost = 'https://'+document.domain;
            @else
        var webHost = 'http://'+document.domain;
        @endif

        let responseInProgress = false;
        var messagebar;
        var messages;
        var msg_query_Interval;
        var pagepath = '{{!empty(Request::has('path')) ? Request::get('path') :''}}'
    </script>

</head>
<body>
<div id="app">
    <div class="view view-main view-init safe-areas">
        @yield('content')
    </div>
    {{--@include('merchant.mobile.run.subview.login-screen')
    @include('merchant.mobile.run.subview.agreement')--}}
</div>
<script type="text/javascript" src="{{asset('css/f7/framework7-bundle81.min.js')}}"></script>
<script type="text/javascript" src="{{asset('css/f7/routes.js')}}"></script>
<script type="text/javascript" src="{{asset('css/f7/store.js')}}"></script>
<script type="text/javascript" src="{{asset('css/f7/app.js')}}?v={{time()}}"></script>
<script type="text/javascript" src="{{asset('js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('css/f7/regionsObject2.js')}}?v={{time()}}"></script>
<script type="text/javascript" src="{{asset('css/f7/cityPicker.js')}}"></script>
<script type="text/javascript" src="{{asset('css/f7/apilist.js')}}?v={{time()}}"></script>
<script src="{{asset('oss/dist/js/plupload/moxie.min.js')}}"></script>
<script src="{{asset('oss/dist/js/plupload/plupload.full.min.js')}}?v=343dfhhkkk4dfhhg"></script>
<script src="{{asset('oss/dist/js/plupload_plug.js')}}"></script>
<script src="{{asset('oss/upindex.js')}}?v={{time()}}"></script>
<script type="text/javascript" src="{{asset('css/f7/index.js')}}?v={{time()}}"></script>
<script type="text/javascript" src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js" charset="utf-8"> </script>
<script>
    var $jq = jQuery.noConflict();
    // 如果当前不是入口页时 返回按键 将返回到入口页

    if(pagepath != ''){
        navigateTo(pagepath,'f7-push');
        pagepath = '';
    }

    // 监听离线
    document.addEventListener('offline', function() {
        console.log('网络离线');
        app.dialog.alert('网络连接已断开，请检查网络设置！');
        app.emit('routerAjaxError', xhr, options);
    });
    // 在路由加载之前检测设备的在线状态
    app.on('routeBeforeEnter', function (routeTo, routeFrom, resolve, reject) {
        // 检测设备是否离线
        if (!navigator.onLine) {
            // 显示错误提示
            app.dialog.alert('网络连接已断开，请检查网络设置！');

            // 拒绝路由加载
            reject();
        } else {
            // 继续路由加载
            resolve();
        }
    });
    /*window.onload = function() {
        // 全局设置
        var app1 = new Framework7({});
        app1.request.setup({
            timeout: 5000, // 设置请求超时为5000毫秒（5秒）
        });
    };*/

    /*var ptrContent = $$('.ptr-content');
    ptrContent.on('ptr:refresh', function (e) {
        alert('ok');
    });*/
    //var ptrs = app.ptr.get('.help-index');
    //ptrs.refresh();

</script>
@stack('js')
</body>
</html>