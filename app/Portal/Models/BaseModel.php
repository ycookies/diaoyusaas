<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $connection = 'dcat';
}
