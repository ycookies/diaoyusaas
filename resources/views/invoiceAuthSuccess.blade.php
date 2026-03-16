<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="origin">
    <meta name="viewport"
          content="width=device-width, viewport-fit=cover, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <title>电票商户授权</title>
    <link rel="stylesheet" href="{{asset('css/weui.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/weui_example.css')}}"/>
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
                <h2 class="weui-msg__title">授权成功</h2>
                {{--<p class="weui-msg__desc">内容详情<a class="weui-wa-hotarea weui-link" href="javascript:">文字链接</a></p>--}}
            </div>
            {{--<div class="weui-msg__opr-area">
                <p class="weui-btn-area">
                    <a href="javascript:history.back();" role="button" class="weui-btn weui-btn_primary">推荐操作</a>
                </p>
            </div>--}}
        </div>
        <img src="" width="100%" alt=""/>
        {{--<div class="weui-cells__title">带图标、说明的列表项</div>--}}
    </div>
    </div>
</div>
</body>

</html>