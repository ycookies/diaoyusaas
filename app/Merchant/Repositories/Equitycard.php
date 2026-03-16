<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Equitycard as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Equitycard extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
