<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\WxappConfig as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class WxappConfig extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
