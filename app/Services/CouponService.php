<?php

namespace App\Services;

use App\Models\Hotel\Coupon;
use App\Models\Hotel\Usercoupon;

/**
 * 优惠券服务
 * @package App\Services
 * anthor Fox
 */
class CouponService extends BaseService {

    /**
     * @desc 检查用优惠券是否可以使用
     * @param $user_id
     * @param array $coupon_ids
     * author eRic
     * dateTime 2024-09-04 11:43
     */
    public static function checkUse($user_id,array $coupon_ids){
        // 检查优惠券是否可用
        $where = [
            ['user_id','=',$user_id],
            ['coupon_status','<>',0],
        ];
        $counts = Usercoupon::where($where)->whereIn('coupon_id', $coupon_ids)->count();
        if(!empty($counts)){
            return false;
        }
        return true;
    }
    /**
     * @desc 核销优惠券
     * @param $user_id
     * @param $coupon_ids
     * @param $order_no
     * author eRic
     * dateTime 2024-09-04 11:35
     */
    public static function hexiao($user_id,array $coupon_ids,$order_no){
        // 更新优惠券信息
        $updata = [
            'sy_time'       => date('Y-m-d H:i:s'),
            'order_no'      => $order_no,
            'coupon_status' => 1,
        ];
        $status = Usercoupon::where(['user_id'=> $user_id])->whereIn('coupon_id', $coupon_ids)->update($updata);
        return true;
    }

    /**
     * @desc 返还优惠券
     * @param $user_id
     * @param $coupon_ids
     * @param $order_no
     * author eRic
     * dateTime 2024-09-04 11:35
     */
    public static function fanhui($user_id,array $coupon_ids){
        // 更新优惠券信息
        $updata = [
            'sy_time'       => null,
            'order_no'      => null,
            'coupon_status' => 0,
        ];
        $status = Usercoupon::where(['user_id'=> $user_id])->whereIn('coupon_id', $coupon_ids)->update($updata);
        return true;
    }
}