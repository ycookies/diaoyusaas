<?php

namespace App\Admin\Repositories\Cgcms;

use App\Models\Cgcms\DownloadFile as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class DownloadFile extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
