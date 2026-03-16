<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MinpayAsynNotify extends HotelBaseModel
{
	const Status_arr = [
	  0 => '失败',
      1 => '成功',
    ];
    protected $table = 'minpay_asyn_notify';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }
}
