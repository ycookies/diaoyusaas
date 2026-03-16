<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MiniprogramPage extends HotelBaseModel
{
    const Type_arr = [
        '' => '全部',
        '1' => '基础页面',
        '2' => '营销页面',
        '3' => '订单页面',
        '4' => '插件页面',
        '5' => '触发功能',
    ];
    const Types_arr = [
        '1' => '基础页面',
        '2' => '营销页面',
        '3' => '订单页面',
        '4' => '插件页面',
        '5' => '触发功能',
    ];
	
    protected $table = 'miniprogram_pages';
    protected $guarded = [];

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }
    
}
