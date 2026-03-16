<?php

namespace App\Admin\Repositories;

use App\Models\MerchantUser as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class MerchantUser extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
