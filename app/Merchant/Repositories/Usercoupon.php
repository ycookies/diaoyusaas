<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Usercoupon as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Usercoupon extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
