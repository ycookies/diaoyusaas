<?php

namespace App\Admin\Repositories\Cgcms;

use App\Models\Cgcms\Ad as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Ad extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
