<?php

namespace App\Portal\Repositories;

use App\Models\CaseTaskTrustOrder as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class CaseTaskTrustOrder extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
