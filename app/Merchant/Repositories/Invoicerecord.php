<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\Invoicerecord as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Invoicerecord extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
