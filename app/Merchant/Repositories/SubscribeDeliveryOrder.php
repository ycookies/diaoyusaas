<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\SubscribeDeliveryOrder as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SubscribeDeliveryOrder extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
