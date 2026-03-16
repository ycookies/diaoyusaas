<?php

namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Menu extends EloquentRepository
{
    public function __construct($modelOrRelations = [])
    {
        $this->eloquentClass = config('merchant.database.menu_model');

        parent::__construct($modelOrRelations);
    }
}
