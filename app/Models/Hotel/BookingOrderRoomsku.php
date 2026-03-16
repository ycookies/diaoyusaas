<?php

namespace App\Models\Hotel;


class BookingOrderRoomsku extends HotelBaseModel {

    protected $table = 'booking_order_roomsku';
    protected $guarded = [];

    // 增加订单对应的房型销售SKU
    public static function add($order_no, $room_sku_id) {
        $roomSkuPrice = RoomSkuPrice::find($room_sku_id);

        $skuinfo = $roomSkuPrice->getOriginal();
        $data = [
            'order_no'            => $order_no,
            'sku_code'            => $skuinfo['sku_code'],
            'hotel_id'            => $skuinfo['hotel_id'],
            'room_id'             => $skuinfo['room_id'],
            'room_sku_id'         => $room_sku_id,
            'roomsku_title'       => $skuinfo['roomsku_title'],
            'roomsku_zaocan'      => $skuinfo['roomsku_zaocan'],
            'roomsku_where'       => $skuinfo['roomsku_where'],
            'roomsku_fuwu'        => $skuinfo['roomsku_fuwu'],
            'roomsku_gift'        => $skuinfo['roomsku_gift'],
            'roomsku_tags'        => $skuinfo['roomsku_tags'],
            'roomsku_give_points' => $skuinfo['roomsku_give_points'],
            'roomsku_give_coupon' => $skuinfo['roomsku_give_coupon'],
            'roomsku_price'       => $skuinfo['roomsku_price'],
        ];
        BookingOrderRoomsku::create($data);

        return true;

    }
}
