<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use App\User;
class IntegralLog extends HotelBaseModel
{
	
    protected $table = 'integral_log';
    protected $guarded = [];

    // 增加积分
    public static function addLog($user_id,$integral,$desc){
        $userinfo = User::where(['id'=> $user_id])->select('hotel_id','point')->first();
        $insdata = [
            'hotel_id'=> $userinfo->hotel_id,
            'user_id'=> $user_id,
            'type'=> 1,
            'integral'=> $integral,
            'total_integral'=> $userinfo->point,
            'desc' => $desc,
        ];
        $res = self::create($insdata);
        return $res;
    }

    // 减少积分
    public static function cutLog($user_id,$integral,$desc){
        $userinfo = User::where(['id'=> $user_id])->select('hotel_id','point')->first();
        $insdata = [
            'hotel_id'=> $userinfo->hotel_id,
            'user_id'=> $user_id,
            'type'=> 2,
            'integral'=> $integral,
            'total_integral'=> $userinfo->point,
            'desc' => $desc,
        ];
        $res = self::create($insdata);
        return $res;
    }
}
