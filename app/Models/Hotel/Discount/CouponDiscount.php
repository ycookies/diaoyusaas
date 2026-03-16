<?php

namespace App\Models\Hotel\Discount;

use App\Services\PriceCalculator;
class CouponDiscount implements Discount
{
    protected $amount;
    protected $title;
    protected $coupon_id;

    public function __construct($amount, $title,$coupon_id = '')
    {
        $this->amount = $amount;
        $this->title = $title;
        $this->coupon_id = $coupon_id;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return [
            'type' => PriceCalculator::Coupon,
            'coupon_id' => $this->coupon_id,
            'title' => $this->title,
            'money' =>  $this->amount
        ];
    }
}