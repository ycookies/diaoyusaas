<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class Hongbao extends HotelBaseModel
{
	
    protected $table = 'hongbao';
    protected $guarded = [];

    public static $status_arr = [
        0 => '停用', 1 => '启用'
    ];
    // 1直接发放2收藏3分享
    public static $grant_type_arr = [
        1 => '直接发放',
        2 => '收藏',
        3 => '分享',
    ];


    // 减去库存
    public function cutNum($hongbao_id,$num = ''){
        if(!empty($num)){
            self::where('id',$hongbao_id)->decrement('number',$num);
        }else{
            self::where('id',$hongbao_id)->decrement('number');
        }
    }
    
}
