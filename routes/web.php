<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MaterialCollectionController;
use App\Http\Controllers\BookingOrderController;
use App\Http\Controllers\Merchant\Mobile\RunController;
use App\Http\Controllers\Merchant\Mobile\KefucenterController;
use App\Http\Controllers\OauthCallbackController;
use App\Models\MerchantUser as Muser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 主页路由
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// 文章相关路由
Route::get('listArticle/{pid}', [IndexController::class, 'listArticle'])->name('listArticle');
Route::get('articleView/{id}', [IndexController::class, 'articleView'])->name('articleView');

// 系统日志路由
Route::group(['prefix' => 'log'], function () {
    Route::get('list', [LogsController::class, 'index']); // 日志列表
    Route::get('info', [LogsController::class, 'info']); // 日志详情
});

// 其他页面路由
Route::get('/goldplan', [HomeController::class, 'goldplan'])->name('goldplan');
Route::get('/material-collect', [MaterialCollectionController::class, 'index'])->name('material-collect');
// Route::get('/booking-order/view', [BookingOrderController::class, 'viewBookingOrder'])->name('booking-order.viewBookingOrder');

// 商户移动端路由
Route::any('/run/login', [RunController::class, 'runLogin']);

// 客服中心路由
Route::any('kefucenter/index', [KefucenterController::class, 'index']);
Route::any('kefucenter/send-msg', [KefucenterController::class, 'sendMsg']);
Route::any('kefucenter/list', [KefucenterController::class, 'kefuLists']);

// 商户运营相关路由组
Route::group([
    'namespace' => 'Merchant\Mobile',
], function () {
    # 客房运营
    Route::any('run/login', [RunController::class, 'runLogin']);
    Route::any('run/logout', [RunController::class, 'runLogout']);
    Route::get('run/home', [RunController::class, 'home'])->name('run.home');
    Route::get('run/order/lists', [RunController::class, 'orderList']);
    Route::get('run/order/detail/{id}', [RunController::class, 'orderDetail']);
    Route::any('run/order/actionSave', [RunController::class, 'actionSave']);
    Route::any('booking-order/view', [RunController::class, 'viewBookingOrder']);
});

// 自动登录路由
Route::get('/autologin/{user}', function (Muser $user) {
    Auth::guard('merchant')->login($user);
    return redirect('/merchant');
})->name('autologin')->middleware('signed');

// 微信开放平台回调路由
Route::any('/wxopenCallback', [OauthCallbackController::class, 'wxopenCallback']);

// 引入商户路由
require_once __DIR__ . '/seller.php';