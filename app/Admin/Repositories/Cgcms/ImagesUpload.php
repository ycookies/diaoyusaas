<?php

namespace App\Admin\Repositories\Cgcms;

use App\Models\Cgcms\ImagesUpload as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ImagesUpload extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
