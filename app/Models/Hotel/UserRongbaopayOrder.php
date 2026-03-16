<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class UserRongbaopayOrder extends HotelBaseModel
{
	
    protected $table = 'user_rongbaopay_order';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id','user_id')->select('id','name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    public static function store($data){
        return self::create($data);

        //return self::create($data);
    }

}
