<?php

use Illuminate\Support\Facades\Route;

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

Route::any('login', 'AuthController@login');
Route::any('retpassword', 'AuthController@retpassword');
Route::post('cancel', 'BookingOrderController@cancel');
// 提供给支付系统的订单各项确认接口
Route::post('orderConfirmAction', 'BookingOrderController@orderConfirmAction');
Route::post('getMinAppQrcode', 'BookingOrderController@getMinAppQrcode');  //根据订单号生成小程序二维码

# 停车场
Route::any('parking/getParkingCarIn', 'ParkingController@getParkingCarIn');  //根据订单号生成小程序二维码
Route::any('parking/getParkingCarOut', 'ParkingController@getParkingCarOut');  //根据订单号生成小程序二维码
Route::any('parking/getParkingChargeInfo', 'ParkingController@getParkingChargeInfo');  //根据订单号生成小程序二维码
Route::any('parking/getParkingCarFee', 'ParkingController@getParkingCarFee');  //根据订单号生成小程序二维码
Route::post('parking/carFreePut', 'ParkingController@carFreePut');  //根据订单号生成小程序二维码


Route::group(['middleware' => ['HotelSellerAuth']], function () {

    # 账号
    Route::any('me', 'AuthController@me');
    Route::any('logout', 'AuthController@logout');
    Route::any('refresh', 'AuthController@refresh');

    # 酒店信息
    Route::any('home', 'IndexController@home');
    Route::any('hotelinfo', 'IndexController@hotelinfo');

    # 订单
    Route::group(['prefix' => 'booking-order'], function () {
        Route::get('lists', 'BookingOrderController@lists');
        Route::get('detail', 'BookingOrderController@detail');
        Route::post('confirm', 'BookingOrderController@confirm');
        Route::post('hexiao', 'BookingOrderController@hexiao');
        Route::post('cancel', 'BookingOrderController@cancel');
    });

    # 客房
    Route::group(['prefix' => 'room'], function () {
        Route::get('lists', 'RoomController@lists');
        Route::get('detail', 'RoomController@detail');
        Route::get('getDatePrice', 'RoomController@getDatePrice');
        Route::post('batchToPrice', 'RoomController@batchToPrice');
        Route::post('actions', 'RoomController@actions');
    });

});
