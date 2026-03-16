<?php

namespace App\Admin\Repositories;

use App\Models\Hotel\BookingOrder as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class BookingOrder extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
