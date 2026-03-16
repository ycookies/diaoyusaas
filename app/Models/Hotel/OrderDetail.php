<?php

namespace App\Models\Hotel;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
	
    use SoftDeletes;

    protected $table = 'order_detail';
    
}
