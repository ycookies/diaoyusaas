<?php

namespace App\Models\Hotel\Order;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class OrderPayResult extends HotelBaseModel
{
    protected $table = 'order_pay_result';
    protected $guarded  = [];
    
}
