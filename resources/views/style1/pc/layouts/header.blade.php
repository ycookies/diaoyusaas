<script src="{{asset('style1/js/jquery.min.js') }}"></script>
<!-- 友好的弹出提示框 -->
{{--{{asset('style1/public/plugins/layer-v3.1.0/layer.js')}}--}}

<!-- 支持子目录 -->
<div id="log">
    <div class="dialog">
        <div class="dialog-head">
            <span class="dialog-close close rotate-hover"></span>
        </div>
        <div class="dialog-body">
            <div class="register">
                <div class="blank-middle"></div>
                <div class="tab text-center">
                    <ul>
                        <li id="signtab1" onclick="setTab('signtab',1,2)" class="cur">登录</li>
                        <!-- <li id="signtab2" onclick="setTab('signtab',2,2)">注册</li> -->
                    </ul>
                </div>
                <!-- 登录开始 -->
                <div id="con_signtab_1">
                    <form method="post" id="popup_login_submit">
                        <!-- 用户名 -->
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type='text' name='username' id='username' class="input text-main radius-none"
                                       value="" data-validate="required:必填" placeholder="用户名">
                                <span class="icon fa fa-user text-gray"></span>
                            </div>
                        </div>
                        <!-- 密码 -->
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type='password' name='password' id="password" class="input text-main radius-none"
                                       placeholder="密码" value=""/>
                                <span class="icon fa fa-key text-gray"></span>
                            </div>
                        </div>
                        <!-- 验证码包括手机验证码专用样式input-group-->
                        <div class="form-group" id="ey_login_vertify">
                            <div class="field field-icon">
                                <div class="input-group">
                                    <input type='text' name='vertify' autocomplete="off"
                                           class="input text-main radius-none" placeholder="图形验证码" value=""/>
                                    <span class="addon"><img
                                                src="{eyou:url link='api/Ajax/vertify' vars='type=users_login' /}"
                                                class="chicuele" id="imgVerifys" onclick="ey_fleshVerify();"
                                                title="看不清？点击更换验证码" align="absmiddle"></span>
                                    <span class="icon fa fa-key text-gray"></span>
                                </div>
                            </div>
                        </div>
                        <!-- 提交 -->
                        <div class="form-button">
                            <input type="hidden" name="referurl" value=""/>
                            <input type="hidden" name="website" value="website"/>
                            <input type="button" name="submit" onclick="popup_login_submit();"
                                   class="button bg-yellow button-large button-block border-none radius-none text-white"
                                   value="提交"/>
                        </div>
                    </form>
                    <p class="margin-top text-right">
                        <a href="{:url('user/Users/reg')}">注册账号</a>&nbsp;|&nbsp;
                        <a href="{:url('user/Users/retrieve_password')}">忘记密码</a>
                    </p>
                </div>
                <!-- 登录end -->

                <!-- 注册开始 -->
                <div id="con_signtab_2" style="display:none;">
                    <form method="post">
                        <!-- 用户名 -->
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type='text' name='username' class="input text-main radius-none" value=""
                                       data-validate="required:必填" placeholder="手机/邮箱/账号">
                                <span class="icon fa fa-user text-gray"></span>
                            </div>
                        </div>

                        <!-- 密码 -->
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type='password' name='password' class="input text-main radius-none" value=""
                                       data-validate="required:必填" placeholder="密码"/>
                                <span class="icon fa fa-key text-gray"></span>
                            </div>
                        </div>

                        <!-- 手机 -->
                        <div class="form-group">
                            <div class="field field-icon">
                                <input type='text' name='username' class="input text-main radius-none" value=""
                                       data-validate="required:必填" placeholder="请输入手机号">
                                <span class="icon fa fa-phone text-gray"></span>
                            </div>
                        </div>

                        <!-- 验证码包括手机验证码专用样式input-group-->
                        <div class="form-group">
                            <div class="field field-icon">
                                <div class="input-group">
                                    <input type='text' name='phone' class="input text-main radius-none"
                                           placeholder="请输入短信验证码" value=""/>
                                    <span class="addon"><img
                                                src="{eyou:url link='api/Ajax/vertify' vars='type=users_login' /}"
                                                class="chicuele" id="imgVerifys" onclick="ey_fleshVerify();"
                                                title="看不清？点击更换验证码" align="absmiddle"></span>
                                    <span class="icon fa fa-phone text-gray"></span>
                                </div>
                            </div>
                        </div>
                        <!-- 提交 -->
                        <div class="form-button">
                            <input type="submit"
                                   class="button bg-yellow button-large button-block border-none radius-none text-white"
                                   value="提交"/>
                        </div>
                    </form>
                </div>
                <!-- 注册end -->

                <!-- 第三方账号登录 -->
                <div id="ey_third_party_login">
                    <div class="blank-middle"></div>
                    <div class="line"><span class="text-gray bg-white text-default">社交账号登录</span>
                        <hr>
                    </div>
                    <div class="blank-small"></div>
                    <div class="bnt-login">
                        <a title="QQ登录" href="{eyou:url link='plugins/QqLogin/login' seo_pseudo='1' seo_inlet='0' /}"
                           class="qq"><i class="fa fa-qq"></i></a>
                        <a title="微信登录" href="{eyou:url link='plugins/WxLogin/login' seo_pseudo='1' seo_inlet='0' /}"
                           class="weixin"><i class="fa fa-weixin"></i></a>
                        <a title="微博登录" href="{eyou:url link='plugins/Wblogin/login' seo_pseudo='1' seo_inlet='0' /}"
                           class="weibo"><i class="fa fa-weibo"></i></a>
                    </div>
                </div>
                <!-- 第三方账号登录 -->
            </div>
        </div>
    </div>
</div>
<!-- 登录弹窗 end -->

<header id="pc-header">
    <div class="layout fixed navbar">
        <div class="container-layout">
            <div class="line">
                <div class="x2 logo">
                    <a href="#" target="_self">
                        <img style="margin-left:80px;" src="{{env('APP_URL')}}/uploads/{{$web_base['web_logo_local'] ?? ''}}"
                                     alt="" class="img-responsive"/></a>
                </div>
                <div class="x9 text-center">
                    <ul class="nav nav-menu nav-inline">
                        <li class="{{$index_active}}"><a href="/" title="首页">首页</a></li>
                        {{--<li class="">
                            <a href="/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=78" class="first-level">
                                赛事资讯<i class="fa fa-angle-down margin-small-left"></i></a>
                            <ul class="drop-menu">
                                <li><a href="/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=79">台钓</a></li>
                                <li><a href="/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=80">路亚</a></li>
                                <li><a href="/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=81">海钓</a></li>
                                <li><a href="/index.php?m=home&amp;c=Lists&amp;a=index&amp;tid=82">全球赛事</a></li>
                            </ul>

                        </li>--}}
                        @foreach ($menu_list as $item)
                            <li class="{{$item->is_active}}" >
                                <a href="{{ $item->a_link }}" @if(!empty($item->target)) target="_blank" @endif class="first-level">
                                    {{$item['title']}}<i class="fa margin-small-left @if(!$item->sub_item->isEmpty()) fa-angle-down @endif"></i></a>
                                @if(!$item->sub_item->isEmpty())
                                    <ul class="drop-menu">
                                        @foreach ($item->sub_item as $ik)
                                        <li>
                                            @if($ik->is_part == 1)
                                                <a href="{{$ik->typelink}}" @if(!empty($ik->target)) target="_blank" @endif>{{$ik->title}}</a>
                                                @else
                                                <a href="{{url('listArticle').'/'.$ik->id}}">{{$ik->title}}</a>
                                                @endif

                                        </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach

                    </ul>
                </div>
                <div class="x1">
                    <div class="ey_htmlid_1609665117">
                    <div class="log-in">
                        <div class="">
                            <a href="{{url('merchant')}}" target="_blank" class="button bg-green radius-rounded border-none text-default">
                                <i class="fa fa-user margin-small-right"></i>商户登陆</a>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!--弹出搜索 -->
{{--<div class="searchBar-m">
    <div class="mask"></div>
    <a href="javascript:void(0)"><i class="fa fa-times"></i></a>
    <div class="form-group">
        {eyou:searchform type='default'}
        <form method="get" action="{$field.action}" onsubmit="return searchForm();">
            <input type="text" name="keywords" id="keywords" class="input radius-none text-middle" value="{eyou:lang name='yybl6' /}" onFocus="this.value=''" onBlur="if(!value){value=defaultValue}"/>
            <button type="submit" name="submit" class="button radius-none border-none" value="Search"/></button>
            {$field.hidden}
        </form>
        {/eyou:searchform}
    </div>
</div>--}}
<!-- 弹出搜索 -->
<div class="clearfix"></div>


<script type="text/javascript">
    //头像下拉
    function head_nav_a() {
        $("#user_nav_z").show();
    }

    function head_nav_b() {
        $("#user_nav_z").hide();
    }

    var GetUploadify_url = "{:url('Uploadify/upload')}";
</script>