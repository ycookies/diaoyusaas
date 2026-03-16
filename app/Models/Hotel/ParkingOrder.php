<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class ParkingOrder extends HotelBaseModel
{
	
    protected $table = 'parking_order';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(\App\Models\Hotel\User::class, 'id', 'user_id')->select('id','name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    public static function addOrder($data){
        return self::create($data);
    }
}
