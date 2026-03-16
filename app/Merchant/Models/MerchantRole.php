<?php

namespace App\Merchant\Models;

use Dcat\Admin\Models\Role;

class MerchantRole extends Role
{
    protected $table = 'merchant_roles';
    protected $guarded = [];
}
