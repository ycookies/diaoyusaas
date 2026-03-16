<?php

namespace App\Listeners;

use App\Models\Hotel\MerchantUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\BookingOrderConfirm;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\WxappConfig;
use App\User;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\User as userModel;
use App\Models\Hotel\HotelSetting;
use App\Services\BookingOrderService;

// 预定确认
class OrderConfirm
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
    public function handle(BookingOrderConfirm $event)
    {
        $hotel_id = $event->order->hotel_id;
        $userinfo = userModel::find($event->order->user_id);


        // 发送订单确认到用户 短信
        $hotel_info = Hotel::where(['id'=> $hotel_id])->select('id','name','link_tel','tel')->first();
        $smsdata = [
            'hotel_name' => $hotel_info->name,
            'hotel_tel' => $hotel_info->tel,
            'order_no' => $event->order->out_trade_no,
            'hedui_code' => $event->order->code,
        ];
        // 给用户发送短信信息
        if(!empty($userinfo->phone)){
            $res = (new \App\Services\SmsService())->sendNotice($userinfo->phone,'SMS_467410320',$smsdata);
        }
        // 给用户发信息 小程序 订阅通知
        $orderinfo    = BookingOrder::with('room', 'hotel', 'user')->where(['out_trade_no' => $event->order->out_trade_no])->first();
        $service1 = (new BookingOrderService())->userMinappMsgtplBookingSuccess($orderinfo);

        // 给用户发信息 公众号模板消息
        $service2 = (new BookingOrderService())->userGzhMsgtplBookingSuccess($orderinfo);
    }
}
