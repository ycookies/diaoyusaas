<?php

namespace App\Models\Hotel;


class Ad extends HotelBaseModel {
    // 1开屏2积分商城3充值4首页5单店版
    const Type_arr = [
        '0' => '首页弹窗',
        '1' => '开屏',
        '2' => '积分商城',
        '3' => '充值',
    ];
    // 1内部，2外部,3跳转
    const State_arr = [
        '1' => '小程序页面',
        '2' => '外部链接',
        //'3' => '跳转小程序',
    ];
    protected $table = 'ad';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }

}
