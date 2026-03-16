<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class HotelDevice extends HotelBaseModel
{
    const Status_0 = 0;
	const Status_1 = 1;
    const Status_arr = [
        0 => '作废',
        1 => '正常',
    ];
    protected $table = 'hotel_device';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }
}
