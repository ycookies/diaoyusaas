<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\InvoiceRegist as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class InvoiceRegist extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
