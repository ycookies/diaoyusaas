<?php

namespace App\Models\Hotel\Order;

use App\Models\Hotel\HotelBaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends HotelBaseModel {
    use SoftDeletes;
    protected $table = 'order';
    protected $guarded = [];

    # 配送方式
    const Send_type_0 = 0; // 快递配送
    const Send_type_1 = 1; // 到店自提
    const Send_type_2 = 2; // 同城配送
    # 是否支付
    const Is_pay_0 = 0; // 未支付
    const Is_pay_1 = 1; // 已支付
    const Is_pay_arr = [
        0 => '<span class="text-danger">未支付</span>',
        1 => '<span class="text-success">已支付</span>',
    ];
    # 支付方式
    const Pay_type_1 = 1; // 在线支付
    const Pay_type_2 = 2; // 货到付款
    const Pay_type_3 = 3; // 余额支付
    #  订单状态
    const Status_0 = 0;
    const Status_1 = 1;
    # 是否评论
    const Is_comment_0 = 0; // 否
    const Is_comment_1 = 1; // 是

    # 订单标识，用于区分插件
    const Sign_tuangou = 'tuangou';
    const Sign_PrepaidCardRecharge = 'prepaid_card_recharge';
    const Sign_HotelRoomBooking = 'hotel_room_booking';

    const Sign_list = [
        Order::Sign_tuangou          => '团购订单',
        Order::Sign_HotelRoomBooking => '酒店预订',
        Order::Sign_PrepaidCardRecharge => '储值卡充值',
    ];

    // 各类业务对应的核销码前缀
    const Sign_hexiaocode_prefix_TG = 'TG'; // 团购订单
    const Sign_hexiaocode_prefix_RB = 'RB'; // 酒店预订

    const Sign_hexiaocode_prefix = [
        Order::Sign_tuangou          => 'TG',
        Order::Sign_HotelRoomBooking => 'RB',
    ];

    protected $appends = ['offline_qrcode_url'];

    public function getOfflineQrcodeUrlAttribute() {
        if (!empty($this->attributes['trade_no'])) {
            return env('APP_URL') . '/hotel/mintools/getQrcode?qrcode_con=' . $this->attributes['offline_qrcode'] . '&hotel_id=' . $this->attributes['hotel_id'];
        } else {
            return '-';
        }
    }

    public function user() {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    // 订单商品信息
    public function goods() {
        return $this->belongsTo(\App\Models\Hotel\Goods\Good::class, 'goods_id');
    }

    // 订单核销
    public function clerk() {
        return $this->belongsTo(\App\Models\Hotel\Order\OrderClerk::class, 'order_id');

    }

    public function detail() {
        return $this->hasOne(\App\Models\Hotel\Order\OrderDetail::class, 'order_id', 'id');
    }

    // 评价
    public function comment() {
        return $this->hasOne(\App\Models\Hotel\Order\OrderComment::class, 'order_id', 'id');
    }

    // 退款
    public function refund() {
        return $this->hasOne(\App\Models\Hotel\OrderRefund::class, 'order_no', 'order_no');
    }

    // 团购订单
    public function tuangouorder() {
        return $this->hasOne(\App\Models\Hotel\Tuangou\TuangouOrderRelation::class, 'order_id', 'id');
    }

    // 门票核销码列表
    public function ticketsCode() {
        return $this->belongsTo(\App\Models\Hotel\TicketsCode::class, 'order_no', 'order_no');
    }

    // 住店评价
    public static function pingjia($order_no) {
        $where  = [
            ['order_no', '=', $order_no],
        ];
        $detail = Order::where($where)->first();
        if ($detail->is_comment == '0') {
            $model             = Order::find($detail->id);
            $model->is_comment = 1;
            $model->save();

            // 触发事件
            //event(new \App\Events\BookingOrderPingjia($detail));
            return true;
        }
        return false;
    }


    /**
     * @desc 更新分账后的剩余金额
     * @param $order_no
     * @param $profitsharing_after_price
     * author eRic
     * dateTime 2025-03-12 11:20
     */
    public static function update_profitsharing_after_price($order_no,$profitsharing_after_price){
        $where = [
            ['order_no','=',$order_no],
            ['profitsharing_after_price','=',null]
        ];
        return \App\Models\Hotel\Order\Order::where($where)->update(['profitsharing_after_price'=>$profitsharing_after_price]);
    }

    /**
     * @desc
     * @param $hotel_id
     * @param $user_id
     * @param $order_no
     * @param $total_price
     * @param $amount
     * @return mixed
     * author eRic
     * dateTime 2025-03-13 12:55
     */
    public function addNewOrder($hotel_id,$user_id,$order_no,$total_price,$amount,$remark){
        $formdata = \App\Models\Hotel\HotelSetting::getlists(['is_booking_profitsharing'], $hotel_id);
        // 生成基础订单
        $ins_order_data = [
            'hotel_id'                   => $hotel_id,
            'user_id'                    => $user_id,
            'order_no'                   => $order_no,
            'is_pay'                     => Order::Is_pay_0,
            'pay_type'                   => 1,
            'total_price'                => $total_price,
            'total_pay_price'            => $amount,
            'send_type'                  => Order::Send_type_1,
            'sign'                       => Order::Sign_PrepaidCardRecharge,
            'is_comment'                 => Order::Is_comment_0,
            'status'                     => Order::Status_0,
            'remark'                     => $remark,
            'total_goods_price'          => $amount, // 订单商品总金额(优惠后)
            'total_goods_original_price' => $total_price, // 订单商品总金额(优惠前)
            'coupon_discount_price'      => 0, // 优惠券的金额
            'discount_info'              => '',
            'is_profitsharing'           => isset($formdata['is_booking_profitsharing']) ? $formdata['is_booking_profitsharing'] : 0,

        ];
        $order_info     = \App\Models\Hotel\Order\Order::create($ins_order_data);

        return $order_info->id;
    }


}
