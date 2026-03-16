<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Coupon as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Coupon extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
