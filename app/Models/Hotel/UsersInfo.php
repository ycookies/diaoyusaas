<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class UsersInfo extends HotelBaseModel
{
	
    protected $table = 'users_info';
    public $timestamps = false;
    public $guarded = [];

}
