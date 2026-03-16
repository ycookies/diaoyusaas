<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\HomeNav as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class HomeNav extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
