<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

class BalanceLog extends HotelBaseModel
{
	
    protected $table = 'balance_log';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id','user_id')->select('id','name','avatar','hotel_id');
    }

    public function hotel() {
        return $this->hasOne(Hotel::class, 'id', 'hotel_id')->select('id','name','ewm_logo');
    }

    public static function addLog($user_id,$add_balance,$desc){
        $userinfo = User::where(['id'=> $user_id])->select('hotel_id','point','balance')->first();
        $insdata = [
            'user_id'     => $user_id,
            'hotel_id'    => $userinfo->hotel_id,
            'type'        => 1,
            'money'       => $add_balance,
            'total_money' => $userinfo->balance,
            'desc'        => $desc,
        ];
        return self::create($insdata);
    }

    public static function cutLog($user_id,$cut_balance,$desc){
        $userinfo = User::where(['id'=> $user_id])->select('hotel_id','point','balance')->first();
        $insdata = [
            'user_id'     => $user_id,
            'hotel_id'    => $userinfo->hotel_id,
            'type'        => 2,
            'money'       => $cut_balance,
            'total_money' => $userinfo->balance,
            'desc'        => $desc,
        ];
        return self::create($insdata);
    }
    
}
