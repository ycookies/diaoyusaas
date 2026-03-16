<?php

namespace App\Http\Controllers\Api\Hotel;

use Illuminate\Http\Request;
use App\Models\Article;
use Orion\Http\Controllers\Controller;
use Orion\Concerns\DisablePagination;
use Illuminate\Support\Facades\Auth;
use Orion\Concerns\DisableAuthorization;

class BaseController extends Controller
{

    // 禁止授权访问
    use DisableAuthorization;

}
