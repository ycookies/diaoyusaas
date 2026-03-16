<?php

namespace App\Models\Hotel\Tuangou;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class TuangouOrder extends HotelBaseModel
{
    protected $table = 'tuangou_orders';
    protected $guarded = [];
    
}
