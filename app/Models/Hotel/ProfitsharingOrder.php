<?php

namespace App\Models\Hotel;


class ProfitsharingOrder extends HotelBaseModel {
    const Status_arr = [
        'wait'       => '待分账',
        'PROCESSING' => '处理中',
        'FINISHED'   => '分账完成',
        'fail'       => '失败',
    ];
    const Status_arr_label = [
        'wait'       => 'gray',
        'PROCESSING' => 'primary',
        'FINISHED'   => 'success',
        'fail'       => 'danger',
    ];
    protected $table = 'hotel_profitsharing_order';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }

    public function receiver() {
        return $this->hasOne(ProfitsharingReceiver::class, 'id', 'receiver_id');
    }

    public function order() {
        return $this->hasOne(BookingOrder::class, 'out_trade_no', 'order_no');
    }

    public static function addOrder($data) {
        $st = self::firstOrCreate(['order_no' => $data['order_no']], $data);
        return $st;
    }
}
