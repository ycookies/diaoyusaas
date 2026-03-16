<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class UserLevel extends HotelBaseModel
{
	const  Level_logo_arr = [
	     'https://hotel.saishiyun.net/images/userlevel/1.png' => '1级图片',
         'https://hotel.saishiyun.net/images/userlevel/2.png' => '2级图片',
         'https://hotel.saishiyun.net/images/userlevel/3.png' => '3级图片',
         'https://hotel.saishiyun.net/images/userlevel/4.png' => '4级图片',
         'https://hotel.saishiyun.net/images/userlevel/5.png' => '5级图片',
         'https://hotel.saishiyun.net/images/userlevel/6.png' => '6级图片',
         'https://hotel.saishiyun.net/images/userlevel/7.png' => '7级图片',
         'https://hotel.saishiyun.net/images/userlevel/8.png' => '8级图片',
         'https://hotel.saishiyun.net/images/userlevel/9.png' => '9级图片',
         'https://hotel.saishiyun.net/images/userlevel/10.png' => '10级图片',
	    ];
    protected $table = 'user_levels';
    // 黑名单字段
    public $guarded = [];

    public function rights() {
        return $this->hasMany(\App\Models\Hotel\UserLevelRight::class, 'level_id', 'id');
    }

}
