<?php

namespace App\Models\Hotel;


class RoomBookingLog extends HotelBaseModel {

    protected $table = 'room_booking_logs';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name', 'avatar', 'hotel_id');
    }

    public function level() {
        return $this->hasOne(UserLevel::class, 'id', 'member_id');
    }

    public function room() {
        return $this->hasOne(Room::class, 'room_id', 'room_id');
    }


    // 添加订单的订房记录
    public static function addlog($order_no) {
        //'arrival_time'   => $arrival_time,    //入住时间
        //'departure_time' => $departure_time, // 离店时间
        $info = self::where(['order_no' => $order_no])->first();
        if ($info) {
            return false;
        }
        $order_info = BookingOrder::where(['order_no' => $order_no])->first();

        $departure_time     = date('Y-m-d', strtotime($order_info->departure_time . " -1 day")); // 去掉离店当天
        $booking_date_range = getDatesInRange($order_info->arrival_time, $departure_time);

        foreach ($booking_date_range as $key => $datetime) {
            $insdata = [
                'user_id'     => $order_info->user_id,
                'hotel_id'    => $order_info->hotel_id,
                'date_time'   => $datetime,
                'room_id'     => $order_info->room_id,
                'room_sku_id' => $order_info->room_sku_id,
                'order_no'    => $order_no,
            ];
            $status  = self::create($insdata);
        }
        return true;
    }

    // 删除这个订单的订房记录
    public static function dellog($data) {
        $info = self::where(['order_no' => $data['order_no']])->delete();
        return $info;
    }
}
