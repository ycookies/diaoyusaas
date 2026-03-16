<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\UsersInfo as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class UsersInfo extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
