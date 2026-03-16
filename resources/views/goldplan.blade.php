<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="origin">
    <meta name="viewport"
          content="width=device-width, viewport-fit=cover, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <title>支付完成</title>
    <link rel="stylesheet" href="{{asset('css/weui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/weui_example.css')}}"/>
    <script type="text/javascript" charset="UTF-8" src="https://wx.gtimg.com/pay_h5/goldplan/js/jgoldplan-1.0.0.js"></script>
    {{--<script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>--}}
    <script type="text/javascript">
            window.onload=function(){
                var mchData ={action:'onIframeReady',displayStyle:'SHOW_CUSTOM_PAGE',height:1680};
                var postData = JSON.stringify(mchData);
                parent.postMessage(postData,'https://payapp.weixin.qq.com');
            }

            function  open_url(url){
                let mchData1 ={action:'jumpOut', jumpOutUrl:'{{$url_link}}'};
                let postData1 = JSON. stringify(mchData1);
                parent.postMessage(postData1,'https://payapp.weixin.qq.com');
            }
            /*wx.config({
                debug: true, // 调试时可开启
                appId: '自己的appid', // <!-- replace -->
                timestamp: 123456, // 必填，填任意数字即可
                nonceStr: 'nonceStr', // 必填，填任意非空字符串即可
                signature: '需要填写微信接口调用后的签名',
                jsApiList: ['chooseImage'], // 必填，随意一个接口即可
                openTagList: ['wx-open-launch-weapp'], // 填入打开小程序的开放标签名
            })
            wx.ready(function (res2) {
                console.log("ready", res2);
                var launchBtn = document.getElementById("launch-btn");

                launchBtn.addEventListener("ready", function (e) {
                    console.log("开放标签 ready");
                });
                launchBtn.addEventListener("launch", function (e) {
                    console.log("开放标签 success");
                });
                launchBtn.addEventListener("error", function (e) {
                    console.log("开放标签 fail", e.detail);
                });
            });

            wx.error(function (err) {
                console.log("error", err);
            });*/
        </script>
    <style>
        body {
            font-family: PingFang SC, "Helvetica Neue", Arial, sans-serif;
        }
        /*.order_box {
            text-align: center;
        }*/

        .order_box .bussiness_avt img {
            width: 100%;
            height: 68px;
            border: 1px solid #E0E0E0;
        }

        .b_name {
            font-size: 14px;
            font-weight: 500;
            color: #333333;
            margin-top: 6px;
        }
        .
    </style>
</head>

<body>
<div class="order_box">
    <div class="container">
    <div class="page">
        <div class="weui-msg" style="padding-top:10px;">
            <div class="weui-msg__icon-area">
                <i class="weui-icon-success weui-icon_msg"></i>
            </div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">{{$oauthinfo->app_name ?? '酒店商户'}}</h2>
                {{--<p class="weui-msg__desc">内容详情<a class="weui-wa-hotarea weui-link" href="javascript:">文字链接</a></p>--}}
            </div>
            {{--<div class="weui-msg__opr-area">
                <p class="weui-btn-area">
                    <a href="javascript:history.back();" role="button" class="weui-btn weui-btn_primary">推荐操作</a>
                </p>
            </div>--}}
        </div>
        {{--<div class="weui-cells__title">带图标、说明的列表项</div>--}}
        <div class="weui-cells">
            <div role="option" class="weui-cell  weui-cell_example">
                <div class="weui-cell__bd"><p>订单号</p>
                </div>
                <div class="weui-cell__ft">{{$out_trade_no}}</div>
            </div>
            <div role="option" class="weui-cell  weui-cell_example">
                <div class="weui-cell__bd"><p>订单状态</p>
                </div>
                <div class="weui-cell__ft">支付成功</div>
            </div>
            {{--<div role="option" class="weui-cell  weui-cell_example">
                <div class="weui-cell__bd"><p>支付金额</p>
                </div>
                <div class="weui-cell__ft">498.00</div>
            </div>--}}
            {{--<div role="option" class="weui-cell  weui-cell_example">
                <div class="weui-cell__bd"><p>小程序链接</p>
                </div>
                <div class="weui-cell__ft">{{$url_link}}</div>
            </div>--}}
        </div>
        <div class="coupou-box" onclick="open_url('{{$url_link}}')">
            <div  class="open_url" >
                <img src="/img/xiaop-banner1.jpg" width="100%">
            </div>
        </div>
    </div>
    </div>
    {{--<div class="bussiness_avt">
        <img id="b_avt" src="https://xxxxxxx/121212.png"
             alt="">
    </div>
    <div class="b_name" id="b_name">
        默认广告占位
    </div>--}}
</div>
</body>

</html>