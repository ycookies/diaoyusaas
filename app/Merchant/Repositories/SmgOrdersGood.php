<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\SmgOrdersGood as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SmgOrdersGood extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
