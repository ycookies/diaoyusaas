<?php

namespace App\Models\Hotel\Order;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class OrderClerk extends HotelBaseModel
{
    use SoftDeletes;
    protected $table = 'order_clerk';
    protected $guarded  = [];

    public function order(){
        return $this->hasOne(\App\Models\Hotel\Order\Order::class, 'id','order_id');
    }
}
