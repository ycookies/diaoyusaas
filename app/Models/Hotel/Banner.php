<?php

namespace App\Models\Hotel;
use Illuminate\Database\Eloquent\Model;

class Banner extends HotelBaseModel
{

    protected $table = 'banner';
    public $guarded = [];

    //public $hidden = ['seller_id','deleted_at'];
    
}
