<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;

class MerchantUser extends HotelBaseModel
{
    protected $connection = 'mysql';
    protected $table = 'merchant_users';

    protected $primaryKey = 'uid';

    public $timestamps = false;

}
