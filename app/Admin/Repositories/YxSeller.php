<?php

namespace App\Admin\Repositories;

use App\Models\Hotel\Seller as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class YxSeller extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
