<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Banner as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Banner extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
