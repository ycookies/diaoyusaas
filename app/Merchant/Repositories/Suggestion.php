<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Suggestion as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Suggestion extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
