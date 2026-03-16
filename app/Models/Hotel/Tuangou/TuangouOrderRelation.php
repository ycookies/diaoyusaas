<?php

namespace App\Models\Hotel\Tuangou;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class TuangouOrderRelation extends HotelBaseModel
{
    protected $table = 'tuangou_order_relation';
    protected $guarded = [];
    protected $appends = ['order_status_txt'];

    const Order_status_1 = 1;
    const Order_status_2 = 2;
    const Order_status_3 = 3;
    const Order_status_4 = 4;
    const Order_status_5 = 5;
    const Order_status_arr = [
        1 => '待付款',
        2 => '未核销',
        //3 => '已核销',
        3 => '已完成',
        4 => '已评论',
        5 => '已退款',
    ];
    public function getOrderStatusTxtAttribute() {
        $arr = self::Order_status_arr;
        return !empty($arr[$this->attributes['order_status']]) ? $arr[$this->attributes['order_status']]:'-';
    }

    public function order(){
        return $this->belongsTo(\App\Models\Hotel\Order\Order::class, 'order_id');
    }

    /*public function goods(){
        return $this->belongsTo(\App\Models\Hotel\Goods\Good::class, 'goods_id');
    }*/
    // 更新订单状态
    public static function upOrderStatus($order_id,$status){
        $TuangouOrderRelation_info = \App\Models\Hotel\Tuangou\TuangouOrderRelation::where(['order_id' => $order_id])->first();
        $TuangouOrderRelation_info->order_status = $status;
        $TuangouOrderRelation_info->save();

        return true;
    }

    
}
