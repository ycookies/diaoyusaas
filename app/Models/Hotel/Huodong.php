<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Huodong extends HotelBaseModel
{
	
    use SoftDeletes;
    const Act_type_arr = ['1'=>'户外运动','2'=>'新品发布','3'=>'品鉴活动'];
    protected $table = 'huodong';
    protected $guarded = [];
    protected $appends =['act_type_txt'];
    
    public function getActTypeTxtAttribute() {
        $act_type = $this->attributes['act_type'];
        $act_type_txt = !empty(self::Act_type_arr[$act_type]) ? self::Act_type_arr[$act_type] : '自定义';
        return $act_type_txt;
    }
    public function hotel() {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }
}
