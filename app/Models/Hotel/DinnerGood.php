<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class DinnerGood extends HotelBaseModel
{
	
    protected $table = 'dinner_goods';


    public function cats(){
        return $this->belongsTo(DinnerGoodsCategory::class,'cid','id');
    }
}
