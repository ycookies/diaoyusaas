<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title> 客服中心 </title>
    <link href="/mui/css/mui.min.css" rel="stylesheet"/>
    <link href="/mui/css/app.css" rel="stylesheet" type="text/css"/>
    <link href="/mui/css/mui.imageviewer.css" rel="stylesheet"/>
    <style>
        html,
        body {
            height: 100%;
            margin: 0px;
            padding: 0px;
            overflow: hidden;
            background-color: #eaeaea;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
        }
        .header{
            position:absolute;
            top:0; /*头部绝对定位位置*/
            height:100px;
            width:100%;
            background: red;
        }
        .mui-bar{
            height:55px !important;
        }
        .mui-title{
            line-height:normal !important;
            padding-top: 10px !important;
        }
        .mui-sub-title{
            font-size: 14px !important;
            color: #ccc !important;
        }
        .footer{
            position:absolute;
            bottom:0;/*尾部绝对定位位置*/
            height:50px;
            width:100%;
            background-color: #fafafa;
            min-height: 50px;
            border-top: solid 1px #bbb;
            padding: 0px 5px;
            padding-right: 80px;
        }
        .main{
            position:absolute;
            width:100%;
            top:44px; /*中间自适应部分绝对定位位置，top是头部的高度*/
            bottom:60px; /*bottom是尾部的高度*/
            background-color: #eaeaea;
            overflow:auto; /*超出的部分，滚动条显示*/
        }
        /*footer {
             position: fixed;
             width: 100%;
             height: 50px;
             min-height: 50px;
             border-top: solid 1px #bbb;
             left: 0px;
             bottom: 0px;
             overflow: hidden;
             padding: 0px 5px;
             padding-right: 80px;
             background-color: #fafafa;
         }*/

        .footer-left {
            position: absolute;
            width: 50px;
            height: 50px;
            left: 0px;
            bottom: 0px;
            text-align: center;
            vertical-align: middle;
            line-height: 100%;
            padding: 12px 4px;
        }

        .footer-right {
            position: absolute;
            width: 80px;
            height: 55px;
            right: 0px;
            bottom: 0px;
            text-align: center;
            vertical-align: middle;
            line-height: 100%;
            padding: 12px 5px;
            display: inline-block;
        }

        .footer-center {
            height: 100%;
            padding: 5px 0px;
        }

        .footer-center [class*=input] {
            width: 100%;
            height: 100%;
            border-radius: 5px;
        }

        .footer-center .input-text {
            background: #fff;
            border: solid 1px #ddd;
            padding: 10px !important;
            font-size: 16px !important;
            line-height: 18px !important;
            font-family: verdana !important;
            overflow: hidden;
        }

        .footer-center .input-sound {
            background-color: #eee;
        }

        .mui-content {
            height: 100%;
            padding: 44px 0px 50px 0px;
            background-color: #eaeaea;
        }

        #msg-list {
            height: 100%;
            -webkit-overflow-scrolling: touch;
            /*padding-bottom: 60px;*/
            /*overflow-x: hidden;*/
            overflow: auto;
        }

        .msg-item {
            padding: 8px;
            clear: both;
        }

        .msg-item .mui-item-clear {
            clear: both;
        }

        .msg-item .msg-user {
            width: 38px;
            height: 38px;
            border: solid 1px #d3d3d3;
            display: inline-block;
            background: #fff;
            border-radius: 3px;
            vertical-align: top;
            text-align: center;
            float: left;
            padding: 3px;
            color: #ddd;
        }

        .msg-item .msg-user-img {
            width: 38px;
            height: 38px;
            display: inline-block;
            border-radius: 3px;
            vertical-align: top;
            text-align: center;
            float: left;
            color: #ddd;
        }

        .msg-item .msg-content {
            display: inline-block;
            border-radius: 5px;
            border: solid 1px #d3d3d3;
            background-color: #FFFFFF;
            color: #333;
            padding: 8px;
            vertical-align: top;
            font-size: 15px;
            position: relative;
            margin: 0px 8px;
            max-width: 75%;
            min-width: 35px;
            float: left;
        }

        .msg-item .msg-content .msg-content-inner {
            overflow-x: hidden;
        }

        .msg-item .msg-content .msg-content-arrow {
            position: absolute;
            border: solid 1px #d3d3d3;
            border-right: none;
            border-top: none;
            background-color: #FFFFFF;
            width: 10px;
            height: 10px;
            left: -5px;
            top: 12px;
            -webkit-transform: rotateZ(45deg);
            transform: rotateZ(45deg);
        }

        .msg-item-self .msg-user,
        .msg-item-self .msg-content {
            float: right;
        }

        .msg-item-self .msg-content .msg-content-arrow {
            left: auto;
            right: -5px;
            -webkit-transform: rotateZ(225deg);
            transform: rotateZ(225deg);
        }

        .msg-item-self .msg-content,
        .msg-item-self .msg-content .msg-content-arrow {
            background-color: #4CD964;
            color: #fff;
            border-color: #2AC845;
        }

        footer .mui-icon {
            color: #000;
        }

        footer .mui-icon:active {
            color: #007AFF !important;
        }

        footer .mui-icon-paperplane:before {
            content: "发送";
        }

        footer .mui-icon-paperplane {
            /*-webkit-transform: rotateZ(45deg);
            transform: rotateZ(45deg);*/

            font-size: 16px;
            word-break: keep-all;
            line-height: 100%;
            padding-top: 6px;
            color: rgba(0, 135, 250, 1);
        }

        #msg-sound {
            -webkit-user-select: none !important;
            user-select: none !important;
        }

        .rprogress {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 140px;
            height: 140px;
            margin-left: -70px;
            margin-top: -70px;
            background-image: url(../images/arecord.png);
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 30px 30px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 5px;
            display: none;
            -webkit-transition: .15s;
        }

        .rschedule {
            background-color: rgba(0, 0, 0, 0);
            border: 5px solid rgba(0, 183, 229, 0.9);
            opacity: .9;
            border-left: 5px solid rgba(0, 0, 0, 0);
            border-right: 5px solid rgba(0, 0, 0, 0);
            border-radius: 50px;
            box-shadow: 0 0 15px #2187e7;
            width: 46px;
            height: 46px;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -23px;
            margin-top: -23px;
            -webkit-animation: spin 1s infinite linear;
            animation: spin 1s infinite linear;
        }

        .r-sigh {
            display: none;
            border-radius: 50px;
            box-shadow: 0 0 15px #2187e7;
            width: 46px;
            height: 46px;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -23px;
            margin-top: -23px;
            text-align: center;
            line-height: 46px;
            font-size: 40px;
            font-weight: bold;
            color: #2187e7;
        }

        .rprogress-sigh {
            background-image: none !important;
        }

        .rprogress-sigh .rschedule {
            display: none !important;
        }

        .rprogress-sigh .r-sigh {
            display: block !important;
        }

        .rsalert {
            font-size: 12px;
            color: #bbb;
            text-align: center;
            position: absolute;
            border-radius: 5px;
            width: 130px;
            margin: 5px 5px;
            padding: 5px;
            left: 0px;
            bottom: 0px;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        #h {
            background: #fff;
            border: solid 1px #ddd;
            padding: 10px !important;
            font-size: 16px !important;
            font-family: verdana !important;
            line-height: 18px !important;
            overflow: visible;
            position: absolute;
            left: -1000px;
            right: 0px;
            word-break: break-all;
            word-wrap: break-word;
        }

        .cancel {
            background-color: darkred;
        }

        .mui-plus .plus {
            display: inline;
        }

        .plus {
            display: none;
        }

        #topPopover {
            position: fixed;
            top: 16px;
            right: 6px;
        }

        #topPopover .mui-popover-arrow {
            left: auto;
            right: 6px;
        }

        p {
            text-indent: 22px;
        }

        span.mui-icon {
            font-size: 14px;
            color: #007aff;
            margin-left: -15px;
            padding-right: 10px;
        }

        .mui-popover {
            height: 300px;
        }

        .mui-content {
            padding: 10px;
            padding-bottom: 0px;
        }

        .msg-item {
            position: relative;
        }

        .name-title {
            position: absolute;
            font-size: 12px;
            top: -15px;
            left: 10px;
            width: 100px;
            height: 22px;
        }
        .mui-preview-image.mui-fullscreen {
            position: fixed;
            z-index: 20;
            background-color: #000;
        }
        .mui-preview-header,
        .mui-preview-footer {
            position: absolute;
            width: 100%;
            left: 0;
            z-index: 10;
        }
        .mui-preview-header {
            height: 44px;
            top: 0;
        }
        .mui-preview-footer {
            height: 50px;
            bottom: 0px;
        }
        .mui-preview-header .mui-preview-indicator {
            display: block;
            line-height: 25px;
            color: #fff;
            text-align: center;
            margin: 15px auto 4;
            width: 70px;
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            font-size: 16px;
        }
        .mui-preview-image {
            display: none;
            -webkit-animation-duration: 0.5s;
            animation-duration: 0.5s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
        }
        .mui-preview-image.mui-preview-in {
            -webkit-animation-name: fadeIn;
            animation-name: fadeIn;
        }
        .mui-preview-image.mui-preview-out {
            background: none;
            -webkit-animation-name: fadeOut;
            animation-name: fadeOut;
        }
        .mui-preview-image.mui-preview-out .mui-preview-header,
        .mui-preview-image.mui-preview-out .mui-preview-footer {
            display: none;
        }
        .mui-zoom-scroller {
            position: absolute;
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            align-items: center;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            left: 0;
            right: 0;
            bottom: 0;
            top: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            -webkit-backface-visibility: hidden;
        }
        .mui-zoom {
            -webkit-transform-style: preserve-3d;
            transform-style: preserve-3d;
        }
        .mui-slider .mui-slider-group .mui-slider-item img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }
        .mui-android-4-1 .mui-slider .mui-slider-group .mui-slider-item img {
            width: 100%;
        }
        .mui-android-4-1 .mui-slider.mui-preview-image .mui-slider-group .mui-slider-item {
            display: inline-table;
        }
        .mui-android-4-1 .mui-slider.mui-preview-image .mui-zoom-scroller img {
            display: table-cell;
            vertical-align: middle;
        }
        .mui-preview-loading {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: none;
        }
        .mui-preview-loading.mui-active {
            display: block;
        }
        .mui-preview-loading .mui-spinner-white {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-left: -25px;
            margin-top: -25px;
            height: 50px;
            width: 50px;
        }
        .mui-preview-image img.mui-transitioning {
            -webkit-transition: -webkit-transform 0.5s ease, opacity 0.5s ease;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
        @-webkit-keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        @-webkit-keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        p img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body contextmenu="return false;">
<header class="mui-bar mui-bar-nav">
    {{--<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left" onclick="closePage();"></a>--}}
    <a id="info" class="mui-icon mui-icon-contact mui-pull-right" href="#bottomPopover"></a>
    <h1 class="mui-title">
        酒店客服中心
    </h1>
    {{--<div class="mui-title"> XXX酒店</div>--}}
</header>
<pre id='h'></pre>
<div class="main">
    <div style="text-align: center;font-size: 14px"> 未找到相关客服信息,请检查</div>
</div>


<!--右下角弹出菜单-->
<div id="bottomPopover" class="mui-popover mui-popover-bottom">
    <div class="mui-popover-arrow"></div>
    <div class="mui-scroll-wrapper">
        <div class="mui-scroll">
            <div class="mui-card">
                <div class="mui-card-header mui-card-media">
                    <img class="mui-pull-left" width="34px" height="34px" style="border-radius: 50px;"
                         src="" onerror="this.src='/img/user1.png'"/>
                    <div class="mui-media-body">
                        微信用户
                    </div>
                    <!--<img class="mui-pull-left" src="../images/logo.png"  />
                    <h2>小M</h2>
                    <p>发表于 2016-06-30 15:30</p>-->
                </div>
                <div class="mui-card-content">
                    <p>联系方式：无</p>
                </div>

            </div>
        </div>
    </div>
</div>
<div id='msg-self-template' style="display: none;">
    <div class="msg-item msg-item-self">
        <i class="msg-user mui-icon mui-icon-person"></i>
        <div class="msg-content">
            <div class="msg-content-inner">
                <span id="msgti"></span>
                <div class="msg-content-arrow"></div>
            </div>
            <div class="mui-item-clear"></div>
        </div>
    </div>
</div>

<div id='sound-alert' class="rprogress">
    <div class="rschedule"></div>
    <div class="r-sigh">!</div>
    <div id="audio_tips" class="rsalert">手指上滑，取消发送</div>
</div>
<script src="/mui/js/mui.min.js"></script>
<script src="/mui/js/mui.imageViewer.js"></script>
<script src="/mui/js/arttmpl.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/mui/js/mui.zoom.js"></script>
<script src="/mui/js/mui.previewimage.js"></script>
{{--<script src="/js/amr/util.js"></script>
<script src="/js/amr/amr.js"></script>
<script src="/js/amr/libamr-min.js"></script>
<script src="/js/amr/amr-player.js"></script>--}}
{{--<script src="/js/amr/lib/pcmdata.min.js"></script>
<script src="/js/amr/lib/swfobject.js"></script>
<script src="/js/amr/libamr-nb.js"></script>
<script src="/js/amr/util.js"></script>
<script src="/js/amr/amr.js"></script>
<script src="/js/amr/decoder.js"></script>
<script src="/js/amr/encoder.js"></script>--}}
{{--<script src="/js/amr/voice-2.0.js"></script>--}}
<script src="/js/amr/libamr-min.js"></script>
<script src="/js/amr/amr-player.js"></script>

{{--<script src="https://g.alicdn.com/dingding/dingtalk-jsapi/2.10.3/dingtalk.open.js"></script>--}}
<script type="text/javascript" charset="utf-8">
    var ele = window.Element;
    mui.previewImage();
    function send() {
        var urls = '{{url('/kefucenter/send-msg?logid='.$logid)}}';
        var msgtxt = $('#msg-text').val();
        if(msgtxt == '')return false;
        $('.loadings').addClass('mui-spinner');
        $.post(urls, {send_content: msgtxt}, function (data) {
            //var data = JSON.parse(d);
            if (data.status == 'success') {
                $('.loadings').removeClass('mui-spinner');
                $('#msgti').html(msgtxt);
                $('#msg-text').val('');
                var telp = $('#msg-self-template').html();
                $('.main').find('#msg-list').append(telp);
                var elekk = document.getElementById('msg-list');
                elekk.scrollTop = ele.scrollHeight;

                //mui.toast('发送成功');
            }else{
                mui.toast('发送失败');
            }
        });
    }
    // 关闭页面
    function closePage() {
        dd.biz.navigation.close({
            onSuccess : function(result) {
            },
            onFail : function(err) {}
        })
    }

    (function ($, doc) {
        var MIN_SOUND_TIME = 800;
        var ele = document.getElementById('msg-list');
        ele.scrollTop = ele.scrollHeight;
        $.init({
            gestureConfig: {
                tap: true, //默认为true
                doubletap: true, //默认为false
                longtap: true, //默认为false
                swipe: true, //默认为true
                drag: true, //默认为true
                hold: true, //默认为false，不监听
                release: true //默认为false，不监听
            }
        });
        template.config('escape', false);
        /*document.getElementById('play_amr').addEventListener('click', function () {
            //var amrurl = $(this).attr('data-amr');
            AmrPlayer.load('https://hotel.rongbaokeji.com/kefu-media/a4XXQ-S0VVxJaWMS2wGB4gB4h8VE8KrmN6xRZ4F0NpxtkgltLzabz6PicKcwJePo.amr').then(function (res) {
                res.connect();
                res.play();
            });
        });*/
    }(mui, document));

    //RongIMLib.RongIMVoice.init();
    function playAMR(amrFile) {

        AmrPlayer.load(amrFile).then(function (res) {
            res.connect();
            res.play();
        });
        /*fetch(amrFile)
            .then(response => response.blob())
            .then(blob => {
                var reader = new FileReader();
                reader.onload = function() {
                    var base64Data = reader.result.split(',')[1];
                    RongIMLib.RongIMVoice.play(base64Data);
                    //console.log(base64Data);
                };
                reader.readAsDataURL(blob);
            })
            .catch(error => {
                console.error('Error fetching or converting AMR URL to base64:', error);
            });*/

        return 'ok';
        var audioContext = new (window.AudioContext || window.webkitAudioContext)();
        var audioElement = new Audio();
        //var amrFile = 'your-amr-file.amr'; // 替换为您的 AMR 文件路径

        fetch(amrFile)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => {
                var blob = new Blob([new DataView(arrayBuffer)], { type: 'audio/amr' });
                var url = URL.createObjectURL(blob);

                audioElement.src = url;
                audioElement.controls = true;
                document.body.appendChild(audioElement);

                audioElement.addEventListener('canplay', function() {
                    var source = audioContext.createMediaElementSource(audioElement);
                    source.connect(audioContext.destination);
                    audioElement.play();
                });
            });
    }

</script>
</body>

</html>