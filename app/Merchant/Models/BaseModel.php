<?php

namespace App\Merchant\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $connection = 'dcat';
}
