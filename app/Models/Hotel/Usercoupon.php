<?php

namespace App\Models\Hotel;


class Usercoupon extends HotelBaseModel {

    const Coupon_status_arr = [
        '0' => '未使用',
        '1' => '已使用',
        '2' => '已过期',
    ];
    public $table = 'coupon_users';
    public $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name','nick_name', 'avatar', 'hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }

    public function coupon() {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }
    // 领取
    public static function receive($data) {
        $where = [
            'user_id'       => $data['user_id'],
            'hotel_id'      => $data['hotel_id'],
            'coupon_id'     => $data['coupon_id'],
            'expire_time'   => $data['expire_time'],
        ];
        $count = self::where($where)->count();
        // 已经领取过
        if($count){
            return false;
        }

        $status = self::create($data);
        // 减少优惠券库存
        Coupon::cutNum($data['coupon_id']);
        return $status;
    }

    // 使用优惠券
    public static function apply($user_id, $coupon_id, $hotel_id, $order_no) {

        $where   = [];
        $where[] = ['id', '=', $coupon_id];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['user_id', '=', $user_id];
        //$where[] = ['status' ,'=', 1];
        //$where[] = ['end_time' ,'<=', date('Y-m-d 23:59:59')];
        $user_coupon = Usercoupon::where($where)->first();
        if (!$user_coupon) {
            return '未找到领取的优惠券信息';
        }

        $where1   = [];
        $where1[] = ['id', '=', $coupon_id];
        $where1[] = ['hotel_id', '=', $hotel_id];
        $info     = Coupon::where($where1)->first();
        if (!$info) {
            return '未找到优惠券信息';
        }
        //
        if (!$info->end_time > date('Y-m-d 23:59:59')) {
            // 更新优惠券 已过期
            $updata = [
                'sy_time'       => date('Y-m-d H:i:s'),
                'coupon_status' => 2,
            ];
            $status = Usercoupon::where($where)->update($updata);
            return '优惠券已过期,无法使用';
        }

        // 优惠券是否满足条件

        // 更新优惠券信息
        $updata = [
            'sy_time'       => date('Y-m-d H:i:s'),
            'order_no'      => $order_no,
            'coupon_status' => 1,
        ];
        $status = Usercoupon::where($where)->update($updata);

        return true;
    }

}
