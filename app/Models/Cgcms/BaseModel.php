<?php

namespace App\Models\Cgcms;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mall\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    //
    protected $connection = 'cgcms';
}
