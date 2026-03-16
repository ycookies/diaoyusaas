<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class UserLevelRight extends HotelBaseModel
{
	
    protected $table = 'user_level_rights';
    public $timestamps = false;
    protected $guarded = [];

}
