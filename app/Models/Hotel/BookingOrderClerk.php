<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class BookingOrderClerk extends HotelBaseModel
{
    const Clerk_type_arr = [
        1 => 'POS机',
        2 => 'PC后台',
        3 => '小程序',
    ];
    const Clerk_type_1 = 'POS机';
    const Clerk_type_2 = 'PC后台';
    const Clerk_type_3 = '小程序';
    protected $table = 'booking_order_clerk';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(MerchantUser::class, 'id', 'user_id');
    }

    public function bookingorder(){
        return $this->hasOne(BookingOrder::class, 'id','order_id');
            //->leftJoin('room as r','r.id','=','booking_order.room_id')
            //->select('booking_order.out_trade_no','r.name as room_name');
    }
}
