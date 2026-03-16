<?php 
use Illuminate\Support\Facades\Route;



Route::group(['prefix'=>'seller','namespace' => 'Seller','middleware'=>['authseller']],function(){

    Route::any('/dashboard','HomeController@dashboard')->name('seller.dashboard');
    Route::any('/home','HomeController@home')->name('seller.home');
});