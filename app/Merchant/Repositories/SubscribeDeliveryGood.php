<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\SubscribeDeliveryGood as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SubscribeDeliveryGood extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
