<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;

class YxSeller extends HotelBaseModel
{
    protected $table = 'seller';
    public $timestamps = false;

}
