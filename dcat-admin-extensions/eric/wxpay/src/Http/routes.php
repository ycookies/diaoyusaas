<?php

use Dcat\Admin\Wxpay\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('wxgzh\wxpay', Controllers\WxpayController::class.'@index');