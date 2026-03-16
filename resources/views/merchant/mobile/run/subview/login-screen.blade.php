{{-- 账户登陆--}}
<div class="login-screen ">
    <div class="page login-screen-page login-bg">

        <div class="page-content login-screen-content" style="background-color: transparent !important;">
            <div class="login-screen-title">律鸟-法律咨询</div>
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
                                    <input type="text" name="username" id="login_username"
                                           placeholder="手机号" value="18966172031">
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
                                           placeholder="密码" value="18966172031abc">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        {{csrf_field()}}
                    </ul>
                    {{--<div class="block-footer right-lox">
                        <a class="item-link" href="/user/retpass"> 忘记密码 </a> <a href="#">&nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp;</a> <a class="item-link" href="/user/lawyer-register"> 账户注册 </a>
                    </div>--}}
                    <div class="float-clear"></div>
                </div>

                <div class="list inset">

                    <ul>
                        <li>
                            <div style="width: 320px;margin:  0px auto;">
                                <a class="button button-large button-round button-fill color-blue" onclick="seller_login()">登 -
                                    陆</a>
                            </div>
                        </li>
                    </ul>
                    <br/>
                    <div class="block-header" style="font-size: 12px">
                        <label class="checkbox"><input type="checkbox" name="is_agreement"/><i
                                    class="icon-checkbox"></i></label>
                        我已阅读并同意 <a href="#" data-popup=".userxieyi" class="popup-open">《用户服务协议》</a> <a href="#"
                                                                                                       data-popup=".yisizhengcei"
                                                                                                       class="popup-open">《律鸟隐私政策》</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- 账户注册--}}
<div class="login-screen register-screen">
    <div class="page login-screen-page" style="">

        <div class="page-content login-screen-content" style="background-color: transparent !important;margin-top: 0px;">
            <div class="top-banner">
                <img src="{{asset('img/bg_lawyer.png')}}" alt="" class="bg">
                <a style="position: absolute;top:20px;right: 20px;color:#FFFFFF"
                   class="link login-screen-close">关闭</a>
                <div class="ban-tx">
                    <div class="tit">律鸟</div>
                    <p class="fg">您的法律事业合作伙伴</p>
                </div>

            </div>
            <div class="login-screen-title">账户注册</div>
            <form class="" id="lawyerRegForm">
                <div class="list list-strong-ios list-dividers-ios inset-ios">
                    <ul>

                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">律师姓名</div>
                                <div class="item-input-wrap">
                                    <input type="text" name="username" id="username" placeholder="请输入您的真实姓名"
                                           value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        {{--<li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">执业证号</div>
                                <div class="item-input-wrap">
                                    <input type="text" name="carid" maxlength="17" id="carid" placeholder="请输入律师执业证号" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>--}}
                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">设置密码</div>
                                <div class="item-input-wrap">
                                    <input type="text" name="password" id="seller_login_password"
                                           placeholder="请设置密码" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">手机号</div>
                                <div class="item-input-wrap">
                                    <input type="text" maxlength="11" name="phone" id="codephone"
                                           placeholder="请输入常用手机号" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">验证码</div>
                                <div class="item-input-wrap" style="position: relative;">
                                    <input type="number" maxlength="11" name="verify_code" id="verify_code"
                                           placeholder="请输入短信验证码" value="">
                                    <span class="input-clear-button"></span>
                                    <a href="#" class="button send_verfiy_code"
                                       style="position: absolute;top:3px;right: 5px;">获取验证码</a>
                                </div>
                            </div>
                        </li>
                        {{csrf_field()}}
                    </ul>
                </div>

                <div class="list inset">

                    <ul>
                        <li>
                            <div style="width: 320px;margin:  0px auto;">
                                <a class="button button-large button-round button-fill color-blue"
                                   id="seller-register-submit">注 - 册</a>
                            </div>
                        </li>
                    </ul>
                    <br/>
                    <div class="block-header" style="font-size: 12px">
                        <label class="checkbox"><input type="checkbox" name="is_register_agreement"
                                                       id="is_register_agreement"/><i
                                    class="icon-checkbox"></i></label>
                        我已阅读并同意 <a href="#" data-popup=".userxieyi" class="popup-open">《用户服务协议》</a> <a href="#"
                                                                                                       data-popup=".yisizhengcei"
                                                                                                       class="popup-open">《律鸟隐私政策》</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- 找回密码--}}
<div class="login-screen retpass-screen">
    <div class="page login-screen-page login-bg">

        <div class="page-content login-screen-content" style="background-color: transparent !important;margin-top: 0px;">
            <div class="top-banner">
                <img src="{{asset('img/bg_lawyer.png')}}" alt="" class="bg">
                <a style="position: absolute;top:20px;right: 20px;color:#FFFFFF"
                   class="link login-screen-close">关闭</a>
                <div class="ban-tx">
                    <div class="tit">律鸟</div>
                    <p class="fg">您的法律事业合作伙伴</p>
                </div>

            </div>
            <div class="login-screen-title">找回账户登陆密码</div>
            <form class="" id="retpassForm">
                <div class="list list-strong-ios list-dividers-ios inset-ios">
                    <ul>

                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">手机号</div>
                                <div class="item-input-wrap">
                                    <input type="text" name="phone" id="retpass_phone" placeholder="填写手机号" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">验证码</div>
                                <div class="item-input-wrap" style="position: relative;">
                                    <input type="text" maxlength="11" name="verify_code" id="retpass_verify_code" placeholder="请输入短信验证码" value="">
                                    <span class="input-clear-button"></span>
                                    <a href="#" class="button send_verfiy_code" style="position: absolute;top:3px;right: 5px;">获取验证码</a>
                                </div>
                            </div>
                        </li>

                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">新密码</div>
                                <div class="item-input-wrap">
                                    <input type="password" name="password" id="retpass_new_password" placeholder="" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        <li class="item-content item-input">
                            <div class="item-inner">
                                <div class="item-title item-label">确认新密码</div>
                                <div class="item-input-wrap">
                                    <input type="password" name="password_confirmation" id="confirm_retpass_new_password" placeholder="" value="">
                                    <span class="input-clear-button"></span>
                                </div>
                            </div>
                        </li>
                        {{csrf_field()}}
                    </ul>
                    <div style="clear:both;"></div>
                </div>

                <div class="list inset">

                    <ul>
                        <li>
                            <div style="width: 320px;margin:  0px auto;">
                                <a class="button button-large button-round button-fill color-blue" onclick="retpass_login_password()">提 交</a>
                            </div>
                        </li>
                    </ul>
                    <br/>
                </div>
            </form>
        </div>
    </div>
</div>