<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MemberRight extends HotelBaseModel
{
	
    protected $table = 'member_rights';
    public $timestamps = false;
    protected $guarded = [];

}
