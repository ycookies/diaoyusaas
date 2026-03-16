<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Seller as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Seller extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
