<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\BookingOrderLidian;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\Hotel;
use App\User;
use App\Models\Hotel\User as userModel;
use App\Models\Hotel\Coupon;
use App\Models\Hotel\CouponUser;
use App\Models\Hotel\HotelSetting;
// 客人离店
class OrderLidian
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
    public function handle(BookingOrderLidian $event)
    {
        $hotel_id = $event->order->hotel_id;
        $userinfo = userModel::find($event->order->user_id);

        // 拿出奖励配置
        $flds     = [
            'user_card_kaika_point',
            'user_booking_point',
            'user_pingjia_point',
            'user_share_valid_days',
            'user_share_balance',
        ];
        $formdata = HotelSetting::getlists($flds,$hotel_id);
        $user_booking_point = !empty($formdata['user_booking_point']) ? $formdata['user_booking_point']:20;
        $user_share_valid_days = !empty($formdata['user_share_valid_days']) ? $formdata['user_share_valid_days']:30;
        $user_share_balance = !empty($formdata['user_share_balance']) ? $formdata['user_share_balance']:20;

        // 发送离店服务短信
        $hotel_info = Hotel::where(['id'=> $hotel_id])->select('id','name','link_tel','tel')->first();
        $smsdata = [
            'hotel_name' => $hotel_info->name,
            'hotel_tel' => $hotel_info->tel,
        ];
        if(!empty($userinfo->phone)){
            $res = (new \App\Services\SmsService())->sendNotice($userinfo->phone,'SMS_467375307',$smsdata);
        }

        // 如果有关注公众号 发送公众号模板消息
        if(!empty($userinfo->gzh_openid)){

        }

        // 给用户加预订次数
        User::addBookingNum($event->order->user_id);

        // 给用户加积分
        User::addPoint($event->order->user_id,$user_booking_point,'酒店预订奖励');

        // 被好友邀请,是否满足奖励条件
        $days_num = two_time_diff_days($userinfo->junior_at,$event->order->created_at);
        if(!empty($userinfo->temp_parent_id) && !empty($userinfo->junior_at) && $days_num <= $user_share_valid_days){
            // 满足条件 给上级 发放奖励
            User::addBalance($userinfo->temp_parent_id,$user_share_balance,'邀请好友住店奖励');
        }

        return true;
    }
}
