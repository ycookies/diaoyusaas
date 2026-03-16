<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\BookingOrderClerk;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\WxappConfig;
use App\User;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\User as userModel;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\CouponUser;
use App\Models\Hotel\HotelSetting;

// 订单核对到店 处理
class OrderClerk
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BookingOrderClerk $event)
    {
        $hotel_id = $event->order->hotel_id;
        $userinfo = userModel::find($event->order->user_id);

        // 发送到店入驻服务短信
        $hotel_info = Hotel::where(['id'=> $hotel_id])->select('id','name','link_tel','tel')->first();
        $smsdata = [
            'hotel_name' => $hotel_info->name,
            'hotel_tel' => $hotel_info->tel,
            'order_no' => $event->order->out_trade_no,
        ];
        if(!empty($userinfo->phone)){
            $res = (new \App\Services\SmsService())->sendNotice($userinfo->phone,'SMS_467495289',$smsdata);
        }

        // 如果有关注公众号 发送公众号模板消息
        if(!empty($userinfo->gzh_openid)){

        }

        return true;


    }
}
