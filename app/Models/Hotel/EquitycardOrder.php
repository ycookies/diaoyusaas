<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class EquitycardOrder extends HotelBaseModel
{
	
    protected $table = 'equitycard_order';

    public static $pay_type_arr = [
        '1' => '微信',
        '2' => '余额',
        '3' => '到店付',
        '4' => '支付宝',
    ];
    // 1季卡2半年卡3年卡
    public static $equitycard_attribute_arr = [
        '1' => '季卡',
        '2' => '半年卡',
        '3' => '年卡',
    ];

    // 1未付款,2已付款
    public static $status_arr =[
        '1' => '未付款',
        '2' => '已付款',
    ];
    
}
