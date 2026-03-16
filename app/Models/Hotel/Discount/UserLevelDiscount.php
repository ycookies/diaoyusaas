<?php

namespace App\Models\Hotel\Discount;

use App\Models\Hotel\WxCardTpl;
use App\Services\PriceCalculator;
use App\User;

class UserLevelDiscount implements Discount {
    protected $amount;
    protected $user_id;
    protected $discount_rate;

    public function __construct($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function handle($basePrice) {
        // 普卡会员
        $user_info = User::where(['id' => $this->user_id])->first();
        if (empty($user_info->card_code)) {
            $this->amount               = 0;
            $this->discount_rate = 0;
            return $this;
        }
        // 获取 会员卡的信息
        $card_info = WxCardTpl::where(['hotel_id' => $user_info->hotel_id, 'status' => 2])->first();
        if ($card_info->discount > 0 && $card_info->discount < 10) {
            $discount            = bcsub(1, $card_info->discount * 0.1, 2);  // discount 的值 为 0.1-10
            $this->discount_rate = formatFloat($card_info->discount);
            $discount_price      = formatFloat(bcmul($basePrice, $discount, 2));
            $this->amount        = $discount_price;
        } else {
            $this->amount = 0;
            $this->discount_rate = 0;
        }


        return $this;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getDescription() {
        return [
            'type'  => PriceCalculator::User_card,
            'title' => '会员折扣(' . $this->discount_rate . '折)',
            'money' => $this->amount
        ];
    }
}