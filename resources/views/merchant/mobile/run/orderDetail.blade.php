
@extends('layouts.F7sellerRun')
@section('title','订房订单')
@section('content')
    <div class="pages" data-name="order-detail">
        <div class="page page-order-detail">
            @component('merchant.mobile.run.component.navbar', ['title' => '订单详情'])
            @endcomponent
            <div class="page-content">

                <div class="block-title">订单信息</div>
                <div class="list list-outline-ios list-strong-ios list-dividers-ios">
                    <ul>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">订单号</div>
                                    <div class="item-after">{{$info->out_trade_no}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">订单类型</div>
                                    <div class="item-after">小程序订房</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">订单金额</div>
                                    <div class="item-after"> {{$info->total_cost}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">订单时间</div>
                                    <div class="item-after"> {{$info->created_at}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">支付状态</div>
                                    <div class="item-after"> {{$info->status_txt}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="block-title">预定人信息</div>
                <div class="list list-outline-ios list-strong-ios list-dividers-ios">
                    <ul>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">预定人</div>
                                    <div class="item-after">{{$info->booking_name}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">预定电话</div>
                                    <div class="item-after">{{$info->booking_phone}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">预定天数</div>
                                    <div class="item-after"> {{$info->days}} 天</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">预定日期</div>
                                    <div class="item-after"> {{$info->arrival_time}} - {{$info->departure_time}}</div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">预定客房</div>
                                    <div class="item-after"> {{$info->room_type}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>


                <div class="block-title">确认订单</div>
                <div class="list list-outline-ios list-strong-ios list-dividers-ios">
                    @if($info->is_confirm == 0)
                    <form id="order-confirm-form">
                        <input type="hidden" name="order_no" value="{{$info->out_trade_no}}">
                        <ul>
                            <li class="item-content item-input item-input-outline">
                                <div class="item-inner">
                                    <div class="item-title item-label">类型</div>
                                    <div class="item-input-wrap" style="margin-top:-10px !important;">
                                        <label class="radio">
                                            {{csrf_field()}}
                                            <input type="radio"  name="confirm_type" value="1">
                                            <i class="icon-radio"></i>
                                        </label>订单确认 &nbsp;&nbsp;&nbsp;&nbsp;
                                        <label class="radio"><input type="radio" name="confirm_type" value="0"><i class="icon-radio"></i></label>订单取消
                                    </div>
                                </div>
                            </li>
                            <li class="item-content item-input item-input-with-info">
                                <div class="item-inner">
                                    <div class="item-input-wrap">
                                        <input type="text" name="confirm_decs" placeholder="非必填">
                                        <span class="input-clear-button"></span>
                                        <div class="item-input-info">备注信息</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <br/>
                        <div style="width: 200px;margin:  0px auto;">
                            <a class="button button-raised button-large button-fill" onclick="orderConfirmSave();">
                                现在确认
                            </a>
                        </div>
                    </form>
                        @else
                        <ul>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title">商家接单状态</div>
                                        <div class="item-after"> {{$info->is_confirm_txt}}</div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    @endif
                </div>
                {{--@if($info->service_status == 1 && !empty($info->rate_number))
                    <div class="block-title">服务评价</div>
                    <div class="list list-strong-ios list-dividers-ios">
                        <ul>
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="item-title">好评星级</div>
                                    <div class="item-after"> {{$info->rate_number ?? '-'}} 星</div>
                                </div>
                            </div>
                            <li class="item-content item-input">
                                <div class="item-inner">
                                    <div class="item-title item-label">评语</div>
                                    <div class="item-input-wrap">
                                        <textarea class=""> {{ $info->rate_message ?? '无' }}</textarea>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                @endif--}}
                <br/>
                {{--<div class="list list-strong">
                    <ul>
                        @if($info->pay_status == 1)
                        <li>
                            <div class="list-button @if($info->service_status == 1) color_green @endif">
                                {{$info->service_status_txt}}
                            </div>
                        </li>
                            @else
                            <li>
                                <div class="list-button color_red ">
                                    未付款
                                </div>
                            </li>
                        @endif
                        @if($info->pay_status == 1 && $info->service_status == 0)

                        <li>
                            @if($info->order_type == 'tuwen')
                            <a href="/seller/order-ask-answer/{{$info->id}}" class="button button-large button-fill color_orange item-link">
                                去解答提问
                            </a>
                            @else
                            <a href="/seller/orderjiaofu/lists/{{$info->id}}" class="button button-large button-fill color_orange item-link">
                                去交付
                            </a>
                            @endif
                        </li>
                        @endif
                    </ul>
                </div>--}}
            </div>
        </div>

    </div>

@endsection
@push('js')
@endpush

