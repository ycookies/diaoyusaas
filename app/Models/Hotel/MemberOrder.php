<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MemberOrder extends HotelBaseModel
{
    const Pay_status0 = 0;
    const Pay_status1 = 1;
    const Pay_status2 = 2;
    const Pay_status = [
        0 => '待支付',
        1 => '已支付',
        2 => '取消支付',
    ];
    protected $table = 'member_orders';
    public $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id','name','avatar','hotel_id');
    }

    public function vipCard() {
        return $this->hasOne(MemberVipSet::class, 'id', 'vipId');
    }

    // 创建一个订单
    public static function createOrder($order_no, $vipid, $user_id,$hotel_id,$detail = '') {
        $info      = MemberVipSet::where(['id' => $vipid])->first();
        $vipExpire = date('Y-m-d H:i:s',strtotime('+'.$info->vip_days.' days'));
        $insdata   = [
            'user_id'   => $user_id,
            'hotel_id' => $hotel_id,
            'order_no'  => $order_no,
            'pay_price' => $info->price,
            'pay_type'  => 0,
            'vipId'     => $vipid,
            'vipExpire' => $vipExpire,
            'detail'    => $detail,
        ];
        $res  = MemberOrder::firstOrCreate(['user_id'=>$user_id,'vipExpire'=> $vipExpire], $insdata);
        if (empty($res->id)) {
            return false;
        }
        return $res->id;
    }
}
