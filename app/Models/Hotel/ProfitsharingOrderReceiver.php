<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class ProfitsharingOrderReceiver extends HotelBaseModel
{
    const Status_arr = [
        'wait' => '分账开始',
        'PROCESSING' => '处理中',
        'FINISHED' => '分账完成',
    ];

    protected $table = 'hotel_profitsharing_order_receiver';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }
    public function receiver() {
        return $this->hasOne(ProfitsharingReceiver::class, 'id', 'receiver_id');
    }

    public function order() {
        return $this->hasOne(BookingOrder::class, 'out_trade_no', 'order_no');
    }

    public static function addOrderReceiver($data){
        $st = self::firstOrCreate(['order_no' => $data['order_no'],'profitsharing_price'=>$data['profitsharing_price']], $data);
        return $st;
    }
}
