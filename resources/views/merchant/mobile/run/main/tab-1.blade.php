<div style="margin: 0px;" class="list list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        <li>
            <a class="item-content link" href="#">
                <div class="item-inner">
                    <div class="item-title" style="color: #8c8c8c;font-weight:700;">{{$hotel->name}}</div>
                    <div class="item-after"></div>
                </div>
            </a>

        </li>
    </ul>
</div>
<div class="block block-strong" style="margin: 0px;">
    <div class="grid grid-cols-3 grid-gap box-price">
        <div>
            <div class="box-num">{{$total_cash}}</div>
            <div class="box-title">总收益</div>
        </div>
        <div>
            <div class="box-num">{{$last_month_total_cash}}</div>
            <div class="box-title">上月</div>
        </div>
        <div>
            <a class="item-link" href="/seller/withdraw/lists">
            <div class="box-num">{{$withdraw_cach}}</div>
            <div class="box-title">本月</div>
            </a>
        </div>
    </div>
</div>
<div style="margin: 0px;" class="list links-list list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        <li>
            <a href="/seller/caller">今日访问人数: 100人</a>
        </li>
    </ul>
</div>
<div class="block-title">最新 未确认 订单</div>
<div class="list media-list  list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        @if (!$new_order_list->isEmpty())
            @foreach ($new_order_list as $items)
                <li>
                    <a class="item-link" href="/run/order/detail/{{$items->out_trade_no}}">
                        <div class="item-content">
                            <div class="item-media">
                                <img src="{{$items->room_logo}}" width="44"/>
                            </div>
                            <div class="item-inner">
                                <div class="item-title-row">
                                    <div class="item-title">预订：{{$items->room_type}}</div>
                                    <div class="item-after">{{$items->total_cost}} 元</div>
                                </div>
                                <div class="item-subtitle">{{$items->created_at}}</div>
                                <div class="item-text text-color-org ">预订人：{{$items->booking_name}}</div>
                                <div class="item-text text-color-org ">预订电话:{{$items->booking_phone}}</div>
                                {{--<div class="item-text">{{$items->subject}}</div>--}}
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        @else
            <li>
                <div class="item-content">
                    <div class="item-media">
                    </div>
                    <div class="item-inner">
                        <div class="item-subtitle">暂无数据!</div>
                    </div>
                </div>

            </li>
        @endif

    </ul>
</div>
