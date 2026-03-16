<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\SubscribeDeliveryOrdersGood as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SubscribeDeliveryOrdersGood extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
