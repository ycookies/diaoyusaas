<?php

namespace App\Services;

use App\Models\Hotel\MemberOrder;
use App\Models\Hotel\WxCardTpl;
use App\User;
use App\Models\Hotel\CouponUser;
use App\Models\Hotel\Coupon;
/**
 * 用户支付服务
 * @package App\Services
 * anthor Fox
 */
class UserPriceService extends BaseService {

    /**
     *  1.超级vip优先
     * @desc 获取用户会员价格
     * @param $user_id
     * @param $price
     * @return string
     * author eRic
     * dateTime 2024-11-14 14:10
     */
    public function getMemberPrice($user_id, $price) {

        // 默认值
        $price_arr = [
            'yuan_price'     => $price, // 原来价格
            'discount'       => 0, // 折扣点
            'discount_price' => $price, // 优惠折扣后的价格
            'youhui_money'   => 0  //节省了多少钱
        ];
        // 优先超级vip
        $where   = [];
        $where[] = ['user_id', '=', $user_id];
        $where[] = ['vipExpire', '>', date('Y-m-d H:i:s')]; // 超级vip 是否过期
        $vipinfo = MemberOrder::with('vipCard')->where($where)->orderBy('id', 'DESC')->first();
        if (!empty($vipinfo->vipCard->discount) && ($vipinfo->vipCard->discount > 0 && $vipinfo->vipCard->discount < 100)) {
            $discount       = $vipinfo->vipCard->discount * 0.01; // discount 的值 为 1-99
            $discount_price = formatFloat(bcmul($price, $discount, 2));
            $youhui_money   = formatFloat(bcsub($price, $discount_price, 2));
            return [
                'yuan_price'     => $price,
                'discount'       => $discount,
                'discount_price' => $discount_price,
                'youhui_money'   => $youhui_money
            ];
        }

        // 普卡会员
        $user_info = User::where(['id' => $user_id])->first();
        if (empty($user_info->card_code)) {
            return $price_arr;
        }
        // 获取 会员卡的信息
        $card_info = WxCardTpl::where(['hotel_id' => $user_info->hotel_id])->first();
        if ($card_info->discount > 0 && $card_info->discount < 10) {
            $discount       = $card_info->discount * 0.1;  // discount 的值 为 0.1-10
            $discount_price = formatFloat(bcmul($price, $discount, 2));
            $youhui_money   = formatFloat(bcsub($price, $discount_price, 2));
            return [
                'yuan_price'     => $price,
                'discount'       => $discount,
                'discount_price' => $discount_price,
                'youhui_money'   => $youhui_money
            ];
        }

        return $price_arr;

    }

    // 获取使用优惠券的支付金额 todo 暂未使用
    public function getUseCouponToPayPrice($user_id,$hotel_id, $price){
        $total_price = $price;
        // 查看优惠券
        $where       = [
            ['user_id', '=', $user_id],
            ['hotel_id', '=', $hotel_id],
            ['coupon_status', '=', 0],
            ['expire_time', '>=', date('Y-m-d H:i:s')],
        ];
        $coupon_user = CouponUser::where($where)->first();
        if ($coupon_user) {
            $couponinfo = Coupon::where(['id' => $coupon_user->coupon_id])->first();
            if (!empty($couponinfo->id)) {
                if (bccomp($total_price, $couponinfo->need_cost) == -1) { // 如何小于，则没有满足金额

                } else {
                    $yuan_price = $total_price;
                    $pay_price  = bcsub($total_price, $couponinfo->cost, 2);
                    $data       = [
                        'coupon_list'  => Coupon::where(['id' => $coupon_user->coupon_id])->get(),
                        'youhui_price' => $couponinfo->cost,
                        'yuan_price'   => formatFloat($yuan_price),
                        'total_price'  => formatFloat($pay_price),
                    ];
                }
            }
        }

        return $data;
    }
}