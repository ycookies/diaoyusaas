<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class HuodongOrder extends HotelBaseModel
{
	
    protected $table = 'huodong_orders';
    protected $guarded = [];
    
    public function hotel()
    {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name', 'avatar', 'hotel_id');
    }

    public function huodong()
    {
        return $this->hasOne(Huodong::class, 'id', 'hd_id');
    }

    public static function addOrder($data)
    {
        return self::create($data);
    }
}
