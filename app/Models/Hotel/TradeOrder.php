<?php

namespace App\Models\Hotel;


class TradeOrder extends HotelBaseModel {
    const Type_101 = 101; // 线上收款
    const Type_102 = 102; // 当面付收款

    public $type_arr = [
        '101' => '小程序普通收款',
        '102' => '当面付收款',
    ];
    public static $pay_status_arr = [
        0 => '未付款',
        1 => '已付款',
    ];
    public static $pay_status_label = [
        0 => 'info',
        1 => 'success',
    ];
    protected $table = 'trade_orders';
    public $guarded = [];
    public $appends = ['pay_status_txt'];

    public function getPayStatusTxtAttribute() {
        return !empty(self::$pay_status_arr[$this->pay_status]) ? self::$pay_status_arr[$this->pay_status]:'-';
    }

    public function user() {
        return $this->hasOne(\App\Models\Hotel\User::class, 'id', 'user_id')->select('id', 'name', 'nick_name','avatar', 'hotel_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    public static function addOrder($data) {
        return self::create($data);
    }

    // 更新订单
    public static function upOrder($out_trade_no,$result){
        $orderinfo = TradeOrder::where(['out_trade_no' => $out_trade_no])->first();
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['result_code']) && $result['result_code'] == 'SUCCESS') {
            
            $date          = \DateTime::createFromFormat('YmdHis', $result['time_end']);
            $formattedDate = date_format($date, 'Y-m-d H:i:s');
            $orderinfo->trade_no   = $result['transaction_id'];
            $orderinfo->pay_time = $formattedDate;
            $orderinfo->pay_status = 1;
            $orderinfo->save();
            return true;
        }

        // 查询后更新订单状态
        if (!empty($status['trade_state']) && $status['trade_state'] == 'SUCCESS') {
            $date          = \DateTime::createFromFormat('YmdHis', $result['time_end']);
            $formattedDate = date_format($date, 'Y-m-d H:i:s');
            $orderinfo->trade_no   = $result['transaction_id'];
            $orderinfo->pay_time = $formattedDate;
            $orderinfo->pay_status = 1;
            $orderinfo->save();
            return true;
        }

        $orderinfo->pay_status = 2;
        $orderinfo->save();

        return false;
    }
}
