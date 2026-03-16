<?php
namespace App\Merchant\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;

class Role extends EloquentRepository
{
    public function __construct($relations = [])
    {
        $this->eloquentClass = config('merchant.database.roles_model');

        parent::__construct($relations);
    }
}
