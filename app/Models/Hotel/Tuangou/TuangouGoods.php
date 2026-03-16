<?php

namespace App\Models\Hotel\Tuangou;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel\HotelBaseModel;

class TuangouGoods extends HotelBaseModel
{
    const Status_arr = [
        0 => '下架',
        1 => '上架',
    ];
    const Status_label = [
        0 => 'danger',
        1 => 'success',
    ];
    protected $table = 'tuangou_goods';
    protected $guarded = [];

    public function goods(){
        return $this->belongsTo(\App\Models\Hotel\Goods\Good::class, 'goods_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Hotel\Goods\GoodsWarehouse::class, 'goods_warehouse_id');
    }

}
