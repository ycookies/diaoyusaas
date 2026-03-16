<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Room as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Room extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
