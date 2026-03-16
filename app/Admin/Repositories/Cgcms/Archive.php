<?php

namespace App\Admin\Repositories\Cgcms;

use App\Models\Cgcms\Archive as Model;
use Dcat\Admin\Repositories\EloquentRepository;


class Archive extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
