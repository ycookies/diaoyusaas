<div class="block-title">聊天通知</div>
<div class="list list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        <li>
            <a class="item-link item-content" href="/seller/im/lists">
                <div class="item-media"><i class="icon f7-icons if-not-md">bubble_left_bubble_right</i></div>
                <div class="item-inner">
                    <div class="item-title">
                        在线咨询
                    </div>
                    <div class="item-after">
                        @if(!empty($im_unread_num))
                        <span class="badge color-yellow">{{$im_unread_num}}</span>
                        @endif
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>
<div class="block-title">全部通知消息</div>
<div class="list list-outline-ios list-strong-ios list-dividers-ios">
    <ul>
        @if (!$new_notive_list->isEmpty())
            @foreach ($new_notive_list as $items)
                <li>
                    <a class="item-link item-content">
                        <div class="item-media"><i class="icon icon-f7">bell</i></div>
                        <div class="item-inner">
                            <div class="item-title">
                                <div class="item-header">{{$items->title}}</div>
                                {{$items->content}}
                            </div>
                            <div class="item-after">未读</div>
                        </div>
                    </a>
                </li>
            @endforeach
        @else
            <li>
                <div class="item-content">
                    <div class="item-media"></div>
                    <div class="item-inner">
                        <div class="item-title">
                            <div class="item-header">暂无数据!</div>

                        </div>
                    </div>
                </div>
            </li>
        @endif
    </ul>
</div>

