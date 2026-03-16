<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

// 活动报名管理
class HuodongUser extends HotelBaseModel
{

    use SoftDeletes;

    protected $table = 'huodong_user';
    protected $guarded = [];

    public function hotel()
    {
        return $this->hasOne(\App\Models\Hotel\Seller::class, 'id', 'hotel_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name','nick_name', 'avatar', 'hotel_id');
    }

    public function huodong()
    {
        return $this->hasOne(Huodong::class, 'id', 'hd_id');
    }

    // 报名
    public static function addbm($hotel_id, $hd_id, $user_id,$bm_name,$bm_phone)
    {
        $data = [
            'hotel_id' => $hotel_id,
            'hd_id' => $hd_id,
            'user_id' => $user_id,
            'bm_name' => $bm_name,
            'bm_phone' => $bm_phone,
        ];
        $res = self::firstOrCreate($data,$data);
    }
}
