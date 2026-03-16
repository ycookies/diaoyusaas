<?php

namespace App\Admin\Repositories;

use App\Models\CaseArticle as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class CaseArticle extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
