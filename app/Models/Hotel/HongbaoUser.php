<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class HongbaoUser extends HotelBaseModel
{
    const Coupon_status_arr = [
        '0' => '未使用',
        '1' => '已使用',
        '2' => '已过期',
    ];
    public $table = 'hongbao_users';
    public $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name', 'avatar', 'hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Seller::class, 'id', 'hotel_id')->select('id', 'name', 'ewm_logo');
    }

    public function coupon() {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }
    // 领取
    public static function receive($data) {

        $status = self::create($data);

        // 减少红包库存
        Hongbao::cutNum($data['hongbao_id']);
        return $status;
    }
}
