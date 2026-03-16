<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\DinnerGoodsCategory as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class DinnerGoodsCategory extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
