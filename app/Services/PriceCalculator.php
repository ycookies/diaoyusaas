<?php

namespace App\Services;

use App\Models\Hotel\Coupon;
use App\Models\Hotel\CouponUser;
use App\Models\Hotel\Discount;
use App\User;

// 负责计算最终价格和管理折扣

class PriceCalculator {

    protected $basePrice; // 原始价格
    protected $discounts = []; // 优惠折扣信息
    protected $finalPrice; // 最后的价格
    protected $hasPrivilegeCardDiscount = false; // 标志，表示是否已应用权益卡折扣
    const User_card = 'user_card';
    const user_vip = 'user_vip';
    const Coupon = 'coupon';
    const Hongbao = 'hongbao';
    const Yajin = 'yajin';

    const Discount_Type = [
        'yyzk_cost'       => 'user_card', // 会员折扣金额
        'equitycard_cost' => 'user_vip', // 权益卡优惠价格
        'dis_cost'        => 'coupon', // 优惠券
        'hb_cost'         => 'hongbao', // 红包
        'yj_cost'         => 'yajin', // 押金金额
    ];

    public function __construct($basePrice) {
        $this->basePrice  = $basePrice;
        $this->finalPrice = $basePrice;
    }

    public function addDiscount($discount) {
        $amount = $discount->getAmount();
        if (empty($amount)) {
            return;
        }
        // 检查是否已应用权益卡折扣
        if ($discount instanceof Discount\PrivilegeCardDiscount) {
            if ($this->hasPrivilegeCardDiscount) {
                return; // 如果已经应用了权益卡折扣，则不再添加
            }
            $this->hasPrivilegeCardDiscount = true; // 设置标志
        }

        // 检查是否已应用会员等级折扣
        if ($discount instanceof Discount\UserLevelDiscount && $this->hasPrivilegeCardDiscount) {
            return; // 如果已经应用了权益卡折扣，则不再添加会员等级折扣
        }
        $this->discounts[] = $discount->getDescription();
        $this->finalPrice  = bcsub($this->finalPrice, $discount->getAmount(), 2);

    }

    public function calculateFinalPrice($userId) {
        // 创建用户等级折扣和权益卡折扣实例
        $privilegeCardDiscount = new Discount\PrivilegeCardDiscount($userId);
        $userLevelDiscount     = new Discount\UserLevelDiscount($userId);


        // 计算并添加折扣金额 (不能变换顺序)
        $this->addDiscount($privilegeCardDiscount->handle($this->basePrice)); // 权益卡折扣
        $this->addDiscount($userLevelDiscount->handle($this->basePrice)); // 用户等级折扣
        $this->couponDiscount($userId);
        return [
            'final_price' => $this->finalPrice,
            'discounts'   => $this->getDiscounts(),
        ];
    }

    // 用户优惠券
    public function couponDiscount($userId) {
        $hotel_id = User::where(['id' => $userId])->value('hotel_id');

        // 查看优惠券
        $where       = [
            ['user_id', '=', $userId],
            ['hotel_id', '=', $hotel_id],
            ['coupon_status', '=', 0],
            ['expire_time', '>=', date('Y-m-d H:i:s')],
        ];
        $coupon_user = CouponUser::where($where)->first();
        //
        if ($coupon_user) {
            $couponinfo = Coupon::where(['id' => $coupon_user->coupon_id])->first();
            if (!empty($couponinfo->id)) {
                if (bccomp($this->basePrice, $couponinfo->need_cost) == -1) { // 如何小于，则没有满足金额

                } else {
                    $this->addDiscount(new Discount\CouponDiscount($couponinfo->cost, $couponinfo->name, $couponinfo->id));
                }
            }
        }
    }

    // 获取优惠总计多少金额
    public function getYouhuiPrice() {
        $youhui_price = bcsub($this->basePrice, $this->finalPrice, 2);
        return formatFloat($youhui_price);
    }

    public function getFinalPrice() {
        return formatFloat($this->finalPrice);
    }

    public function getDiscounts() {
        foreach ($this->discounts as $key => $item) {
            if (is_numeric($item['money']) && $item['money'] > 0) {

            } else {
                unset($this->discounts[$key]);
            }
        }
        return $this->discounts;
    }

    // 获取各种优惠金额
    public static function getTypeYouhuiPrice(array $discounts, $price_type) {
        $price          = 0;
        $new_price_type = !empty(self::Discount_Type[$price_type]) ? self::Discount_Type[$price_type] : '';
        if (empty($new_price_type)) {
            return $price;
        }
        foreach ($discounts as $key => $items) {
            if ($items['type'] == $new_price_type) {
                $price = $items['money'];
            }
        }
        return $price;
    }

    // 获取单个优惠信息
    public static function getTypeYouhuiInfo(array $discounts, $price_type) {
        $info          = [];
        $new_price_type = !empty(self::Discount_Type[$price_type]) ? self::Discount_Type[$price_type] : '';
        if (empty($new_price_type)) {
            return $info;
        }
        foreach ($discounts as $key => $items) {
            if ($items['type'] == $new_price_type) {
                $info = $items;
            }
        }
        return $info;
    }
}