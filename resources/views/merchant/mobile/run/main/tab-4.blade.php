<div class="content">
    <div class="list list-outline-ios list-strong-ios list-dividers-ios">
        <ul>
            <li>
                <a class="item-link item-content" href="/seller/profile">
                    <div class="item-media">
                        <img class="" width="44"
                             src="{{!empty(Auth::guard('run')->user()->avatar)? Auth::guard('run')->user()->avatar : asset('img/toux1.png')}}">
                        {{--<img class="" width="44"
                             src="{{asset('img/toux1.png')}}">--}}

                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            {{Auth::guard('run')->user()->name ?? '用户'}} (ID:{{Auth::guard('run')->user()->id}})
                        <div class="item-header"></div>
                        </div>
                        <div class="item-after"></div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="list list-outline-ios list-strong-ios list-dividers-ios">
        <ul>
            {{--<li>
                <a class="item-link item-content" href="/seller/lawyer/arch">
                    <div class="item-inner">
                        <div class="item-title">客户管理</div>
                        <div class="item-after arch_status_txt">
                            24
                        </div>
                    </div>
                </a>
            </li>--}}

            {{--<li>
                <a class="item-link item-content" href="/seller/service/create">
                    <div class="item-inner">
                        <div class="item-title">服务售卖</div>
                        <div class="item-after sold_service_status_txt">
                            @if(!empty($sold_service))
                             已设置
                                @else
                                未设置
                            @endif
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a class="item-link item-content" href="/seller/withdraw/binding">
                    <div class="item-inner">
                        <div class="item-title">提现账号绑定</div>
                        <div class="item-after withdraw_binding_status_txt">
                        </div>
                    </div>
                </a>
            </li>--}}
        </ul>
    </div>

    {{--<div class="list links-list list-outline-ios list-strong-ios list-dividers-ios">
        <ul>
            <li>
                <a href="/seller/setting">通用</a>
            </li>
            <li>
                <a href="/seller/help/index">帮助中心</a>
            </li>
            <li>
                <a class="item-content item-link" href="/seller-about">关于律鸟</a>
            </li>
        </ul>
    </div>--}}

    <div class="list list-strong">
        <ul>
            <li>
                <a class="list-button external" href="/run/logout">退出登陆</a>
            </li>
        </ul>
    </div>

</div>