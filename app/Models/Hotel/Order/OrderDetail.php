<?php

namespace App\Models\Hotel\Order;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class OrderDetail extends HotelBaseModel
{
    use SoftDeletes;
    protected $table = 'order_detail';
    protected $guarded = [];


    public function order(){
        return $this->belongsTo(\App\Models\Hotel\Order\Order::class, 'order_id','id');
    }

    // 订单商品信息
    public function goods(){
        return $this->belongsTo(\App\Models\Hotel\Goods\Good::class, 'goods_id');
    }

    public function getGoodsInfoAttribute(){
        if(!empty($this->attributes['goods_info'])){
            return json_decode($this->attributes['goods_info'],true);
        }
        return $this->attributes['goods_info'];
    }
}
