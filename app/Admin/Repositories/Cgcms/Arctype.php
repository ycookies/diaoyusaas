<?php

namespace App\Admin\Repositories\Cgcms;

use App\Models\Cgcms\Arctype as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Arctype extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
