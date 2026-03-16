<?php

namespace App\Models\Hotel\Order;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotel\HotelBaseModel;

class OrderComment extends HotelBaseModel
{
    protected $table = 'order_comments';
    protected $guarded = [];
    protected $appends = ['pic_url_arr','score_icon'];

    public function getPicUrlArrAttribute() {
        if (!empty($this->attributes['pic_url'])) {
            return explode(',',$this->attributes['pic_url']);
        } else {
            return [];
        }
    }
    // 获取星图标
    public function getScoreIconAttribute() {
        $icon_html = '';
        for ($i=1;$i <= $this->attributes['score'];$i++){
            $icon_html .= '<i class="fa fa-star text-orange-2"></i>';
        }
        return $icon_html;
    }

    public static function adds($data){
        $res = self::firstOrCreate(['order_id' =>$data['order_id']],$data);
        return $res;
    }
    public function order(){
        return $this->hasOne(\App\Models\Hotel\Order\Order::class, 'id','order_id');
    }

    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }
    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id','name','avatar','hotel_id');
    }

    public function order_detail() {
        return $this->hasOne(\App\Models\Hotel\Order\OrderDetail::class, 'id', 'order_detail_id');
    }
}
