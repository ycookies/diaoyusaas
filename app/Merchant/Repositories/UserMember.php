<?php

namespace App\Merchant\Repositories;

use App\Models\Hotel\UserMember as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class UserMember extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
