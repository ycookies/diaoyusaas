<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class SmgOrder extends HotelBaseModel
{
	
    protected $table = 'smg_order';
    // 1待支付,2待收货,3已完成,4退款中,5退款完成,6取消
    public static $status_arr = [
        1 => '待支付',
        2 => '待收货',
        3 => '已完成',
        4 => '退款中',
        5 => '退款完成',
        6 => '取消',
    ];
    
}
