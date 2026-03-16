<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HomeNav extends HotelBaseModel
{
	
    use SoftDeletes;

    protected $table = 'home_nav';
    
}
