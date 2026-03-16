<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\EquitycardOrder as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class EquitycardOrder extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
