<?php

namespace App\Models\Hotel\Discount;
use App\User;
use App\Models\Hotel\WxCardTpl;
use App\Models\Hotel\MemberOrder;
use App\Services\PriceCalculator;

class PrivilegeCardDiscount implements Discount
{
    protected $amount;
    protected $user_id;
    protected $discount_rate;
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function handle($basePrice){

        // 优先超级vip
        $where   = [];
        $where[] = ['user_id', '=', $this->user_id];
        $where[] = ['trade_no', '!=', null];
        $where[] = ['pay_status', '=', 1];
        $where[] = ['vipExpire', '>', date('Y-m-d H:i:s')]; // 超级vip 是否过期
        $vipinfo = MemberOrder::with('vipCard')->where($where)->orderBy('id', 'DESC')->first();
        if (!empty($vipinfo->vipCard->discount) && ($vipinfo->vipCard->discount > 0 && $vipinfo->vipCard->discount < 100)) {
            $discount       = bcsub(1 ,($vipinfo->vipCard->discount * 0.01),2); // discount 的值 为 1-99
            $this->discount_rate = formatFloat(bcmul($vipinfo->vipCard->discount , 0.1,2));
            $discount_price = formatFloat(bcmul($basePrice, $discount, 2));
            $this->amount = $discount_price;
        }
        return $this;
    }
    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return [
            'type' => PriceCalculator::user_vip,
            'title' => '权益卡折扣('.$this->discount_rate.'折)',
            'money' =>  $this->amount
        ];
    }
}