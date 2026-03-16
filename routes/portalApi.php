<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Orion\Facades\Orion;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// 'middleware'=>['authseller'] 'namespace' => 'App\Api\Controllers'
//
Route::any('banner/lists',      'IndexController@banner');
Route::any('nav/lists',      'IndexController@nav');

Route::any('hotel/lists',      'HotelController@lists');
Route::any('hotel/detail',      'HotelController@detail');

Route::any('user/wxUserLoginOrRegiser',      'UserController@wxUserLoginOrRegiser');
# 各类通知
Route::any('/notify/wxPayNotifyWx8c3d9b0bbf9272bc',      'NotifyController@wxPayNotifyWx8c3d9b0bbf9272bc');

# 用户登陆 获取token
Route::any('user/wxlogin',      'UserController@wxlogin');

# 需要 token
Route::group(['middleware' => ['portalapi']],function(){

    Route::any('user/getUserinfo',      'UserController@getUserinfo');

    # 客房预定 订单
    Route::any('getBookingOrderList',      'OrderController@getBookingOrderList');
    Route::any('getBookingOrderDetail',      'OrderController@getBookingOrderDetail');
    # 酒店预定
    Route::post('/booking/preorder',      'OrderController@prepayOrder');
});
