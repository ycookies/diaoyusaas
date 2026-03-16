<?php

namespace App\Models\Hotel;


class RechargeOrder extends HotelBaseModel {

    protected $table = 'recharge_orders';
    protected $guarded = [];


    // 创建一个订单
    public static function createOrder($order_no, $user_id, $hotel_id, $recharge_price, $give_price) {
        $insdata = [
            'user_id'        => $user_id,
            'hotel_id'       => $hotel_id,
            'order_no'       => $order_no,
            'recharge_price' => $recharge_price,
            'give_price'     => $give_price,
        ];
        $res     = RechargeOrder::firstOrCreate(['order_no' => $order_no], $insdata);
        if (empty($res->id)) {
            return false;
        }
        return $res->id;
    }
}
