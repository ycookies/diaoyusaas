<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\SubscribeWake as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SubscribeWake extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
