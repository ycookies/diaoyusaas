<div class="block-title">全部订单</div>
<div class="list media-list  list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        @if (!$all_order_list->isEmpty())
            @foreach ($all_order_list as $items)
                <li>
                    <a class="item-link" href="run/order/detail?order_no={{$items->out_trade_no}}">
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
