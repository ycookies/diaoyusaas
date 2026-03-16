<?php

use Dcat\Admin\Alipay\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('ali/alipay', Controllers\AlipayController::class.'@index');