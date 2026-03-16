<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class OrderRefund extends HotelBaseModel
{
	
    protected $table = 'order_refund';
    public $guarded = [];

    const Status1 = 1;
    const Status2 = 2;
    const Status3 = 3;

    const Status_arr = [
        1 => '审核中',
        2 => '已通过',
        3 => '已拒绝',
    ];

    public function user() {
        return $this->hasOne(\App\Models\Hotel\User::class, 'id', 'user_id')->select('id','name','nick_name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    public function room() {
        return $this->hasOne(\App\Models\Hotel\Room::class, 'id', 'room_id')->select('id','name','logo');
    }

    /**
     * @desc 创建退款处理记录
     * @param $order 退款订单
     * @param $refund_reason 退款原因
     * @param $sign 业务类型
     * @return mixed
     * author eRic
     * dateTime 2025-01-12 20:52
     */
    public static function createOrder($order,$refund_reason,$sign ,$operator = '用户'){
        // 创建预订退款订单
        if($sign == 'hotel_room_booking'){ // 预订客房订单
            //$out_request_no = 'RE' . $order->order_no;
            $refund_price   = $order->total_fee;
            $fee_price      = $refund_price;
            $room_id = \App\Models\Hotel\BookingOrder::where(['order_no'=> $order->order_no])->value('room_id');
            $refund_decs = $refund_reason;
            $mdata       = [
                'hotel_id'       => $order->hotel_id,
                'room_id'        => $room_id,
                'user_id'        => $order->uid,
                'order_no'       => $order->order_no,
                'out_request_no' => $order->refund_no,
                'sign'           => $sign,
                'cost'           => $refund_price,
                'fee_price'      => $fee_price,
                'refund_desc'    => $refund_decs,
                'status'         => OrderRefund::Status1,
                'operator' => $operator,
            ];
            $res_status  = OrderRefund::firstOrCreate(['order_no' => $order->order_no], $mdata);
            return $res_status;
        }

    }

    // 更新退款订单状态
    public static function upStatus($order_no,$status){
        $TuangouOrderRelation_info = \App\Models\Hotel\OrderRefund::where(['order_no' => $order_no])->first();
        $TuangouOrderRelation_info->status = $status;
        $TuangouOrderRelation_info->save();
        return true;
    }
}
