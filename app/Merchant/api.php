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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('test', 'Api\ApiTestController@test');
//Orion::resource('article', 'Api\\'.ArticleController::class);
// 'as' => 'api.'
Route::group(['as' => 'api.'], function() {
    Orion::resource('article', 'Api\\'.ArticleController::class);
});
