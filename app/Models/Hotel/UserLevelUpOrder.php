<?php

namespace App\Models\Hotel;


class UserLevelUpOrder extends HotelBaseModel {
    const Pay_status0 = 0;
    const Pay_status1 = 1;
    const Pay_status2 = 2;
    const Pay_status = [
        0 => '待支付',
        1 => '已支付',
        2 => '取消支付',
    ];
    protected $table = 'user_level_up_orders';
    public $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name', 'avatar', 'hotel_id');
    }

    public function level() {
        return $this->hasOne(UserLevel::class, 'id', 'member_id');
    }

    // 创建一个订单
    public static function createOrder($order_no, $level_id, $user_id, $hotel_id, $detail = '') {
        $info = UserLevel::where(['id' => $level_id])->first();
        //$vipExpire = date('Y-m-d H:i:s',strtotime('+'.$info->vip_days.' days'));
        $insdata = [
            'user_id'   => $user_id,
            'hotel_id'  => $hotel_id,
            'order_no'  => $order_no,
            'pay_price' => $info->buy_price,
            'pay_type'  => 0,
            'detail'    => $detail,
            'level_id'  => $level_id,
            'level_num' => $info->level_num,
        ];
        $res     = UserLevelUpOrder::create($insdata);
        if (empty($res->id)) {
            return false;
        }
        return $res->id;
    }
}
