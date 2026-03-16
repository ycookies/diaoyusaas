<?php

namespace App\Models\Hotel;


class CouponUser extends HotelBaseModel {

    protected $table = 'coupon_users';
    protected $guarded = [];

    public function coupon() {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }

    public function user() {
        return $this->hasOne(\App\User::class, 'id','user_id')->select('id','name','nick_name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

}
