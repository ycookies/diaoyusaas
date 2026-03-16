<?php

namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Permission extends EloquentRepository
{
    public function __construct()
    {
        $this->eloquentClass = config('merchant.database.permissions_model');

        parent::__construct();
    }
}
