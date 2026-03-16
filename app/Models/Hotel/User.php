<?php

namespace App\Models\Hotel;

use Dcat\Admin\Admin;
class User extends HotelBaseModel {
    const Status_arr = [
        '0' => '禁止',
        '1' => '正常'
    ];
    const Status_label = [
        '0' => 'danger',
        '1' => 'success'
    ];
    const User_source_arr = [
        'rongb_plat' => '融宝平台',
        'wx_min'     => '微信小程序',
        'ali_min'    => '支付宝小程序',
        'douy_min'   => '抖音小程序',
    ];
    protected $table = 'user';
    //public $timestamps = false;
    public $guarded = [];

    public function level() {
        return $this->hasOne(UserLevel::class, 'id', 'level_id')->where(['hotel_id'=> Admin::user()->hotel_id]);
    }

    // 增加定房次数
    public static function addBookingNum($user_id){
        User::where('id',$user_id)->decrement('booking_num');
        return true;
    }

}
