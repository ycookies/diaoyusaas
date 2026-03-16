<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class UserLevelUpLog extends HotelBaseModel
{
	
    protected $table = 'user_level_up_logs';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(\App\User::class, 'id', 'user_id')->select('id', 'name', 'avatar', 'hotel_id');
    }

    public function level() {
        return $this->hasOne(UserLevel::class, 'id', 'member_id');
    }
}
