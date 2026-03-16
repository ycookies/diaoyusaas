<?php

namespace App\Models\Hotel;


class Coupon extends HotelBaseModel {

    protected $table = 'coupons';
    protected $guarded = [];
    //protected $appends = ['is_receive','receive_txt'];
    public static $status_arr = [
        0 => '停用', 1 => '启用'
    ];
    // 1直接发放2收藏3分享
    public static $grant_type_arr = [
        1 => '直接发放',
        2 => '收藏小程序',
        3 => '分享邀请好友',
        4 => '新入会'
    ];


    // 减去库存
    public static function cutNum($coupon_id, $num = '') {
        if (!empty($num)) {
            self::where('id', $coupon_id)->decrement('number', $num);
        } else {
            self::where('id', $coupon_id)->decrement('number');
        }
    }


}
