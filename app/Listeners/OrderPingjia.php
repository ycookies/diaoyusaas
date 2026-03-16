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
// 住店评价
class OrderPingjia
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
        //$userinfo = userModel::find($event->order->user_id);

        // 拿出奖励配置
        $flds     = [
            'user_pingjia_point',
        ];
        $formdata = HotelSetting::getlists($flds,$hotel_id);
        $user_pingjia_point = !empty($formdata['user_pingjia_point']) ? $formdata['user_pingjia_point']:10;


        // 给用户奖励积分
        User::addPoint($event->order->user_id,$user_pingjia_point,'住店评价奖励');

        return true;
    }
}
