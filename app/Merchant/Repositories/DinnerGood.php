<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\DinnerGood as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class DinnerGood extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
