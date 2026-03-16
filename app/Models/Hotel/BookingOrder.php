<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Admin;

class BookingOrder extends HotelBaseModel
{
    const Status1 = 1;
    const Status2 = 2;
    const Status3 = 3;
    const Status4 = 4;
    const Status5 = 5;
    const Status6 = 6;
    const Status7 = 7;
    const Status8 = 8;

    const isReceived_0 = 0;
    const isReceived_1 = 1;
    const isReceived_2 = 2;

    const confirm_type_0 = 0; // 待确认
    const confirm_type_1 = 1; // 确认订房
    const confirm_type_2 = 2; // 取消订房

	
    protected $table = 'booking_order';
    protected $guarded = [];

    protected $appends = ['status_txt','is_confirm_txt','type_txt','code_qrcode'];
    // 1未付款,2已付款，3取消,4完成,5已入住,6申请退款,7退款,8拒绝退款
    public static $status_arr = [
        '' => '全部',
        1 => '未付款',
        2 => '已付款',
        3 => '已取消',
        4 => '已完成',
        5 => '已入住',
        6 => '申请退款',
        7 => '已退款',
        8 => '拒绝退款',
        9 => '已确认'
    ];

    public static $status_arr1 = [
        '' => '全部',
        1 => '待付款',
        2 => '待入驻',
        //3 => '已取消',
        4 => '已完成',
        /*5 => '已入住',
        6 => '申请退款',
        7 => '已退款',
        8 => '拒绝退款',
        9 => '已确认'*/
    ];
    // 0等待接单 1确认接单 2取消接单
    public static $Is_confirm = [
        '' => '全部',
        0 => '待确认',
        1 => '已确认',
        2 => '已取消'
    ];
    public static $type_arr = [
        1 => '微信支付',
        2 => '余额支付',
        3 => '到店付',
        4 => '支付宝',
    ];
    public function getStatusTxtAttribute()
    {
        return !empty(self::$status_arr[$this->attributes['status']]) ? self::$status_arr[$this->attributes['status']]:'-';
    }
    public function getIsConfirmTxtAttribute()
    {
        return !empty(self::$Is_confirm[$this->attributes['is_confirm']]) ? self::$Is_confirm[$this->attributes['is_confirm']]:'-';
    }

    public function getTypeTxtAttribute()
    {
        if(!empty($this->attributes['type'])){
            return !empty(self::$type_arr[$this->attributes['type']]) ? self::$type_arr[$this->attributes['type']]:'';
        }else{
            return  '-';
        }
    }
    // 获取核对码二维码
    public function getCodeQrcodeAttribute()
    {
        if(!empty($this->attributes['trade_no'])){
            return env('APP_URL').'/hotel/mintools/getQrcode?qrcode_con='.$this->attributes['code'].'&hotel_id='.$this->attributes['hotel_id'];
        }else{
            return  '-';
        }
    }

    public function invoice() {
        return $this->hasOne(\App\Models\Hotel\Invoicerecord::class, 'goods_order_no','out_trade_no')->select('id','user_id','goods_order_no','fuwu_type','hotel_id','orderNo','buyerName','buyerTaxNum');
    }

    public function user() {
        return $this->hasOne(\App\User::class, 'id','user_id')->select('id','name','nick_name','openid','gzh_openid','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    public function room() {
        return $this->hasOne(\App\Models\Hotel\Room::class, 'id', 'room_id')->select('id','name','logo','price');
    }

    // 简单的房型sku销售信息 用于列表
    public function roomsku_brief() {
        return $this->hasOne(\App\Models\Hotel\BookingOrderRoomsku::class, 'order_no', 'order_no')->select('roomsku_title','roomsku_zaocan');
    }

    // 完整的房型sku销售信息 用于详情
    public function roomsku() {
        return $this->hasOne(\App\Models\Hotel\BookingOrderRoomsku::class, 'order_no', 'order_no');
    }

    // 退款记录
    public function refund(){
        return $this->hasOne(\App\Models\Hotel\OrderRefund::class, 'order_no', 'order_no');
    }

    // 退款订单
    public function refunds(){
        return $this->hasOne(\App\Models\Hotel\Refund::class, 'order_no', 'order_no');
    }


    // 预定确认
    public static function orderConfirm($out_trade_no){
        $where = [
            ['out_trade_no','=',$out_trade_no],
        ];

        $detail = BookingOrder::where($where)->first();
        if($detail->is_confirm == 0){
            $model = BookingOrder::find($detail->id);
            $model->is_confirm = 1;
            //$model->status = 9;
            $model->confirm_time = date('Y-m-d H:i:s');
            $model->save();
            // 给用户发送通知
            event(new \App\Events\BookingOrderConfirm($detail));
        }
        return true;
    }

    // 预定取消
    public static function orderCancel($out_trade_no){
        $where = [
            ['out_trade_no','=',$out_trade_no],
        ];

        $detail = BookingOrder::where($where)->first();
        if($detail->is_confirm == 0){
            $model = BookingOrder::find($detail->id);
            $model->status = 3; // 订单取消
            $model->save();
            // 触发退款

            // 给用户发送通知
            //event(new \App\Events\BookingOrderConfirm($detail));
        }
        return true;
    }

    // 核对到店
    public static function daodian($out_trade_no,$clerk_code,$user_id,$clerk_type = 1){
        $where = [
            ['out_trade_no','=',$out_trade_no],
            ['code','=',$clerk_code]
        ];

        $detail = BookingOrder::where($where)->first();
        if($detail->voice == 1){
            $model = BookingOrder::find($detail->id);
            $model->voice = 2;
            $model->status = 5;
            $model->dd_time = date('Y-m-d H:i:s');
            $model->save();
            $insdata = [
                'user_id'      => $user_id,
                'clerk_type'   => $clerk_type,
                'clerk_remark' => '',
                'hotel_id'     => $detail->hotel_id,
                'clerk_code' => $clerk_code,
                'order_id'     => $detail->id,
                'order_no'     => $detail->out_trade_no,
            ];
            $info    = BookingOrderClerk::firstOrCreate(['order_no' => $detail->out_trade_no], $insdata);
            // 触发通知
            event(new \App\Events\BookingOrderClerk($detail));
        }
    }



    // 客人离店
    public static function lidian($out_trade_no){
        $where = [
            ['out_trade_no','=',$out_trade_no],
        ];
        $detail = BookingOrder::where($where)->first();
        if(empty($detail->js_time)){
            $model = BookingOrder::find($detail->id);
            $model->status = 4;
            $model->js_time = date('Y-m-d H:i:s');
            $model->save();

            // 分账
            if($detail->type == 1){ // 只有微信支付 才能分账
                $res = (new \App\Services\ProfitsharingService())->profitsharing($out_trade_no);
                if($res !== true){
                    info($res.'-'.$out_trade_no);
                }
            }

            // 触发通知
            event(new \App\Events\BookingOrderLidian($detail));
            return true;
        }
        return false;
    }

    // 住店评价
    public static function pingjia($out_trade_no){
        $where = [
            ['out_trade_no','=',$out_trade_no],
        ];
        $detail = BookingOrder::where($where)->first();
        if(!empty($detail->js_time) && $detail->is_assess == '0'){
            $model = BookingOrder::find($detail->id);
            $model->is_assess = 1;
            $model->save();

            // 触发事件
            event(new \App\Events\BookingOrderPingjia($detail));
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
        return \App\Models\Hotel\BookingOrder::where($where)->update(['profitsharing_after_price'=>$profitsharing_after_price]);
    }

}
