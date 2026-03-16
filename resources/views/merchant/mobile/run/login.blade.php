@extends('layouts.F7sellerRun')
@section('title','酒店客房管理')
@section('content')
    <div class="page page-current" data-name="seller-login">
        {{--<div class="navbar">
            <div class="navbar-bg"></div>
            <div class="navbar-inner">
                <div class="title">律师登陆</div>
            </div>
        </div>--}}
        <div class="page-content login-bg">
            <div class="block">
                <div class="login-logo" style="text-align: center;margin-top:100px;font-size: 33px;font-weight: bold">
                    融宝易住
                </div>
                <p style="text-align: center">
                    专注酒店订房及营销
                </p>
            </div>
            <form class="" id="loginForm">
                <div class="list list-strong-ios list-dividers-ios inset-ios">
                    <ul>

                        <li class="item-content item-input">
                            {{--<div class="item-media">
                                <i class="f7-icons alarm_fill"></i>
                            </div>--}}
                            <div class="item-inner">
                                <div class="item-title item-label">手机号</div>
                                <div class="item-input-wrap">
                                    {{csrf_field()}}
                                    <input type="text" name="username" id="login_username"
                                           placeholder="手机号" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        <li class="item-content item-input">
                            {{--<div class="item-media">
                                <i class="f7-icons alarm_fill"></i>
                            </div>--}}
                            <div class="item-inner">
                                <div class="item-title item-label">账户密码</div>
                                <div class="item-input-wrap">
                                    <input type="password" name="password"
                                           id="login_password"
                                           placeholder="填写账户密码" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        {{csrf_field()}}
                    </ul>
                    {{--<div class="block-footer right-lox">
                        <a href="#" data-popup=".retpass-screen" class="text-color-grey popup-open">忘记密码</a>
                        <a href="#">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</a>
                        <a href="#" data-popup=".register-screen" class="text-color-grey popup-open">账户注册</a>
                    </div>--}}
                    <div style="clear:both;"></div>
                </div>

                <div class="list inset">

                    <ul>
                        <li>
                            <div style="width: 320px;margin:  0px auto;">
                                <a class="button button-large button-round button-fill color-blue" onclick="seller_login()">登 - 陆</a>
                            </div>
                        </li>
                    </ul>
                    <br/>
                    <div class="block-header" style="font-size: 12px">
                        <label class="checkbox"><input type="checkbox" name="is_agreement" /><i class="icon-checkbox"></i></label>
                        我已阅读并同意 <a href="#" data-popup=".userxieyi" class="popup-open">《用户服务协议》</a><a href="#" data-popup=".yisizhengcei" class="popup-open">《隐私政策》</a>
                    </div>
                </div>
            </form>
            {{--@if(alipay_or_weixin() == 'weixin')
                <div style="text-align: center;margin-top: 120px">
                    <a class="external" style="color: #868EA3" href="/wxgzh/auth?type=seller_login&callback={{ urlencode('/seller/home') }}">
                        <img style="border-radius: 50px;width: 44px" src="{{asset('images/wxlogo100.png')}}" alt=""/>
                        <div>微信登陆</div>
                    </a>
                </div>
            @endif--}}
        </div>
    </div>
@endsection
@push('js')
    <script>
        //loginPage();
    </script>
@endpush
