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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('user/wxMinLogin','UserController@wxMinLogin'); // 小程序用户登陆
Route::post('user/wxMinRegister','UserController@wxMinRegister');

Route::any('web1','DemoController@web1');

//Route::any('test', 'Api\ApiTestController@test');
//Orion::resource('article', 'Api\\'.ArticleController::class); ['as' => 'api.']
// 'as' => 'api.'
// 'middleware' => ['auth:api']
Route::group(['middleware' => ['auth:api']], function() {
    Orion::resource('article', ArticleController::class);
    Orion::resource('user', UserController::class);
    //Orion::resource('room', RoomController::class);
    // 客房
    //Route::get('room', 'RoomController@index');

    // 超市商品
    Route::get('dinnergoods', 'DinnerGoodController@index');

    # 客房
    //Route::get('lists',      'RoomController@lists');
    Route::group(['prefix' => 'room'], function (){
        Route::get('index',      'RoomController@index'); # 列表
        //Route::get('detail',    'HotelController@detail'); # 详情
        //Route::get('models',    'HotelController@models'); # 详情
    });

    // 超市
    Route::group(['prefix' => 'dinner'], function (){
        Route::get('lists',      'DinnerGoodController@lists'); # 列表
        //Route::get('detail',    'HotelController@detail'); # 详情
        //Route::get('models',    'HotelController@models'); # 详情
    });

    //Route::get('dinnergoods', 'DinnerGoodController@index');

    # 酒店
    Route::group(['prefix' => 'info'], function (){
        //Route::get('rows',      'HotelController@rows'); # 列表
        Route::get('detail',    'InfoController@detail'); # 详情
        Route::get('room',    'InfoController@rooms'); # 详情
    });

    # 预定
    Route::group(['prefix' => 'order'], function (){
        Route::get('model',     'OrderController@model'); # 详情
        Route::get('ticket',    'OrderController@ticket'); # 优惠券
        Route::get('trade',     'OrderController@trade'); # 费用明细
        Route::post('book',     'OrderController@book'); # 生成订单
        Route::get('score',     'OrderController@score'); # 积分
        Route::post('pay',      'OrderController@pay'); # 发起支付
        Route::post('pay-success',  'OrderController@paySuccess'); # 支付成功
    });
});



Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

    //  首页配置信息
    Route::get('/home/index', 'HomeController@index');
});


/*# 用户
Route::group(['prefix' => 'user'], function (){
    Route::post('login',    'UserController@login'); # 用户登录
    Route::post('mobile',   'UserController@mobile'); # 用户手机号
    Route::get('info',      'UserController@info'); # 用户信息
});*/
