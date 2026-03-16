<?php

use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Merchant\Models\MerchantUser;
Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->resource('/member-user',MemberUserController::class);

    # 开放接口
    $router->get('openapi-docs', 'OpenApiDocsController@index');

    # 全局配置
    $router->get('web-config', 'WebConfigController@index');
    $router->post('web-config/save', 'WebConfigController@saveData');

    # 测试专用
    $router->get('demo', 'DemoController@index');
    $router->get('payv3-cert', 'CertController@index');

    $router->get('/', 'HomeController@index');
    $router->resource('case-article', CaseArticleController::class);
    $router->resource('seller', YxSellerController::class);
    $router->resource('merchant-users', MerchantUserController::class);
    $router->resource('booking-order', BookingOrderController::class);
    $router->resource('booking-order', BookingOrderController::class);
    $router->resource('booking-order', BookingOrderController::class);

    # 日志
    $router->resource('logs/apilogs', ApilogController::class);

    # 酒店
    $router->resource('hotelinfo', HotelInfoController::class);

    # 分账相关
    $router->resource('profitsharing-receiver', ProfitsharingReceiverController::class);
    $router->resource('profitsharing-order', ProfitsharingOrderController::class);
    $router->resource('profitsharing-order-receiver', ProfitsharingOrderReceiverController::class);

    # cgcms
    $router->any('cgcms/configs', 'Cgcms\ConfigsController@index');
    $router->any('cgcms/configs/formsave', 'Cgcms\ConfigsController@formSave');
    $router->resource('cgcms/webad', Cgcms\AdController::class);
    $router->resource('cgcms/arctype', Cgcms\ArctypeController::class);
    $router->resource('cgcms/archive', Cgcms\ArchiveController::class);
    $router->resource('cgcms/partner', Cgcms\PartnerController::class);

    # 上传资源
    $router->any('uploads-web', 'UploadsController@uploadsWeb')->name('admin.uploads-web');
    $router->any('upload/storage', 'UploadsController@storage')->name('upload.storage');
    $router->any('uploads', 'UploadsController@handle')->name('admin.uploads');
    $router->any('uploads/verifyFile', 'UploadsController@verifyFile')->name('admin.uploads.verifyFile');


    $router->resource('room', RoomController::class);// 客房信息

    $router->get('gold-plan', 'GoldPlanController@index');// 点金计划
    $router->get('gold-plan/{id}', 'GoldPlanController@detail');
    $router->any('goldplan/actions', 'GoldPlanController@actions');

    # 全局配置
    $router->get('settings', 'Setting\SettingsController@index');
    $router->get('settings/oss', 'Setting\OssController@index');
    $router->get('settings/pay', 'Setting\PayController@index');
    $router->post('settings/save', 'Setting\SettingsController@formSave');
    $router->post('hotelsettings/save', 'Setting\HotelSettingActionController@formSave');

    # 日志管理

    $router->resource('minpay-asyn-notify', MinpayAsynNotifyController::class);// 转发交易日志
    $router->any('minpay-asyn-notify/resetAsynNotify', 'OverallController@resetAsynNotify');// 重新转发交易日志

    # 设备管理
    $router->resource('hotel-device', HotelDeviceController::class);

    #  商户资料进件 后台管理
    $router->resource('jinjiandoc', JinjianDocController::class);

    # 商户资料进件 对外表单
    Route::get('material-collect/create-link', 'MaterialCollectionController@createLink');
    Route::post('material-collect/create-link/save', 'MaterialCollectionController@createLinkSave')->name('material-collect.createLinkSave');
    Route::get('material-collect/form', 'MaterialCollectionController@index')->name('material-collect.form');
    Route::any('material-collect/form/save', 'MaterialCollectionController@formSave')->name('material-collect.formSave');

    # 商户入驻平台资料收集
    Route::get('ruzhu/form', 'RuzhuController@index')->name('ruzhu.form');
    Route::any('ruzhu/form/save', 'RuzhuController@formSave')->name('ruzhu.formSave');


    # 账号管理
    Route::post('merchant-user-handle/resetPassword', 'MerchantUserHandleController@resetPassword')->name('merchant-user-handle.resetPassword');
    Route::get('merchant-user-handle/editAccount', 'MerchantUserHandleController@editAccount')->name('merchant-user-handle.editAccount');
    Route::any('merchant-user-handle/editAccountSave', 'MerchantUserHandleController@editAccountSave')->name('merchant-user-handle.editAccountSave');

    # 客服中心
    $router->resource('kefucenter', HotelKefucenterController::class);


    # 微信小程序模板管理
    Route::any('wxminapp_template', 'WxMinappTemplateController@index');

    # 微信开放平台 第三方应用 授权管理
    $router->resource('wxopen-oauth', WxopenOauthController::class);


    Route::get('/autologin/{user}', function (MerchantUser $user) {
        Auth::login($user);

        return redirect()->home();
    })->name('autologin')->middleware('signed');
});
