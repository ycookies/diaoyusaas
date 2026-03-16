<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class UserMember extends HotelBaseModel
{
	
    protected $table = 'user_member';
    protected $guarded = [];

    // 黑名单字段
    //public $guarded = ['api_token'];
    //public $timestamps = false;

    public function user() {
        return $this->hasOne(User::class, 'id', 'uid');
    }

    public static function addUser($data){
        $status = self::firstOrCreate(['uid'=>$data['uid'],'hotel_id'=> $data['hotel_id']], $data);
        return $status;
    }
}
