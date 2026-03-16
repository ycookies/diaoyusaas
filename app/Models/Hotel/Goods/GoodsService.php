<?php

namespace App\Models\Hotel\Goods;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;

class GoodsService extends HotelBaseModel
{
	
    use SoftDeletes;

    protected $table = 'goods_services';
    protected $guarded = [];
}
