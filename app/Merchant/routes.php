<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    // 采集同程酒店房间信息
    $router->get('room-caiji-tools','Helpers\RoomCaijiToolsController@index');

    $router->get('test','TestController@index');

    // 小程序配置及上架
    $router->get('minapp', 'WxMinapp\MinappController@index'); // 小程序配置
    $router->post('wxaCommit', 'WxMinapp\MinappController@wxaCommit'); // 提交小程序
    $router->get('yisiSettingView', 'WxMinapp\MinappController@yisiSettingView'); // 提交小程序隐私信息
    $router->post('yisiSettingSave', 'WxMinapp\MinappController@yisiSettingSave'); // 提交小程序隐私信息
    $router->get('afreshOauth', 'WxMinapp\MinappController@afreshOauth'); // 小程序配置
    $router->get('viewMinappInfo', 'WxMinapp\MinappController@viewMinappInfo'); // 查看小程序信息
    $router->any('saveMinappPayconfig', 'WxMinapp\MinappController@saveMinappPayconfig'); // 保存小程序支付参数
    $router->post('wxaBindTester', 'WxMinapp\MinappController@wxaBindTester'); // 小程序体验人员
    $router->post('wxaUpdateshowwxaitem', 'WxMinapp\MinappController@wxaUpdateshowwxaitem'); // 设置扫码关注的公众号
    $router->post('changeVisitstatus', 'WxMinapp\MinappController@changeVisitstatus'); // 设置小程序的服务状态


    #
    # helpers
    $router->get('helpers-tools/scaffold', 'Helpers\ScaffoldController@index');
    $router->post('helpers-tools/scaffold', 'Helpers\ScaffoldController@store');
    $router->post('helpers-tools/scaffold/table', 'Helpers\ScaffoldController@table');
    $router->get('helpers-tools/icons', 'Helpers\IconController@index');
    $router->resource('helpers-tools/extensions', 'Helpers\ExtensionController'); // ['only' => ['index', 'store', 'update']]

    # 公众号管理
    $router->get('wxgzh', 'Wxgzh\GzhController@index'); // 微信公众号配置
    $router->get('gzh-afreshOauth', 'Wxgzh\GzhController@afreshOauth'); // 小程序配置
    $router->get('viewWxgzhInfo', 'Wxgzh\GzhController@viewWxgzhInfo'); // 查看公众号信息
    $router->any('wxgzh/getMenuList', 'Wxgzh\GzhMenuController@getMenuList');
    $router->post('wxgzh/updateMenu', 'Wxgzh\GzhMenuController@updateMenu'); // 更新公众号菜单


    # 公众号 卡券
    $router->resource('wxgzh/card/tpl', Wxgzh\WxCardTplController::class);
    $router->any('wxgzh/card/index', 'Wxgzh\CardController@index');
    $router->any('wxgzh/card/detial', 'Wxgzh\CardController@getDetail');
    $router->post('wxgzh/card/addCard', 'Wxgzh\CardController@addCard');
    $router->any('wxgzh/card/delCard', 'Wxgzh\CardController@delCard');
    $router->any('wxgzh/card/createQrCode', 'Wxgzh\CardController@createQrCode');


    $router->resource('storeinfo', StoreInfoController::class); // 酒店信息
    $router->resource('room', RoomController::class);// 客房信息
    $router->resource('room-sheshi', RoomSheshiController::class);// 客房设施配置
    $router->get('room-batch-edit', 'RoomBatchEditController@index');
    $router->post('room-batch-edit-save', 'RoomBatchEditController@batchTiaojiaSave');// 批量调价保存

    $router->resource('room-tiaojia-logs', RoomTiaojiaLogController::class);// 客房设施配置

    $router->resource('booking-order', BookingOrderController::class); // 订房订单
    $router->resource('booking-order-refund', OrderRefundController::class); // 订房订单退款
    // OrderRefundController.php
    $router->resource('user-member', UserMemberController::class); // 基础会员
    $router->resource('user-booking-member', UserBookingMemberController::class); // 订房会员

    $router->resource('user-level', UserLevelController::class); // 会员管理
    $router->resource('user-center-nav', UserCenterNavController::class); // 用户中心菜单展示

    // FinanceController

    $router->get('finance', 'FinanceController@index'); // 收入明细
    $router->get('finance/booking/{id}', 'FinanceController@bookingOrderDetail'); // 收入明细
    $router->get('finance/vipbuy/{id}', 'FinanceController@vipbuyDetail'); // 收入明细
    $router->get('finance/tuangou/{id}', 'FinanceController@tuangouDetail'); // 收入明细

    $router->resource('seller', SellerController::class);
    $router->any('seller-infosave', 'StoreInfoController@infosave'); // 保存酒店信息

    # 管理员设置
    $router->resource('/ad/users', AdminUser\AdUserController::class);
    $router->resource('/ad/roles', AdminUser\AdRoleController::class);

    # 商户参数管理
    $router->resource('hotel-setting', HotelSettingController::class);
    $router->post('hotel-setting-edit', 'HotelSettingActionController@edit');

    # 日常事务
    $router->any('booking-order-list','DailyAction\OrderQueryController@index');
    $router->any('booking-order-list/{id}','DailyAction\OrderQueryController@detail');
    $router->any('booking-order-confirm','DailyAction\OrderQueryController@orderConfirm');

    $router->any('hedui-daodian','DailyAction\HeduiDaodianController@heduiDaodian');
    $router->any('hedui-daodian/check','DailyAction\HeduiDaodianController@heduiDaodianCheck');

    $router->any('order-hexiao','DailyAction\HeduiDaodianController@heduiDaodian');
    $router->any('order-hexiao/check','DailyAction\HeduiDaodianController@heduiDaodianCheck');

    $router->any('order-jiesuan','DailyAction\HeduiDaodianController@orderJiesuan');
    $router->any('order-jiesuan-save','DailyAction\HeduiDaodianController@orderJiesuanSave');

    // 扫码购
    $router->resource('dlingoods-list', DinnerGoodController::class);
    $router->resource('dlingoods-cats', DinnerGoodsCategoryController::class);
    $router->resource('dlingoods-order', DinnerOrderController::class);
    $router->resource('dlingoods-order-goods', DinnerOrdersGoodController::class);

    $router->resource('smg-goods-cats', SmgGoodsCategoryController::class);
    $router->resource('smg-goods', SmgGoodController::class);
    $router->resource('smg-goods-order', SmgOrdersGoodController::class);

    // 房价
    $router->resource('room-calendar', RoomCalendarController::class);// 房价日历
    $router->resource('room-price-set', RoomPriceSetController::class); // 房价设置
    $router->post('calendar-price-save', 'RoomPriceHandleController@calendarPriceSave'); // 房价设置
    $router->post('calendar-sku-price-save', 'RoomPriceHandleController@calendarSkuPriceSave'); // 房型房价设置
    $router->resource('room-sku-price', RoomSkuPriceController::class); // 房型价格列表
    $router->resource('room-sku-gift', RoomSkuGiftController::class); // 房型价格赠送礼包
    $router->resource('room-sku-tags', RoomSkuTagController::class); // 房型价格权益标签
    $router->resource('room-sku-calendar', RoomSkuCalendarController::class);// 房型Sku房价日历
    $router->resource('room-sku-price-set', RoomSkuPriceSetController::class);// 房型Sku房价日历
    $router->resource('room-sku-batch-edit', RoomSkuBatchEditController::class);// 房型Sku房价日历
    $router->post('room-sku-batch-edit-save', 'RoomSkuBatchEditController@skuBatchTiaojiaSave');// 批量调价保存
    // 住中服务
    $router->resource('suggestion', SuggestionController::class); //意见箱管理
    $router->resource('subscribe-wake', SubscribeWakeController::class); //预约叫醒
    $router->resource('subscribe-delivery-good', SubscribeDeliveryGoodController::class); //预约送物

    // 营销中心
    $router->resource('coupon', CouponController::class); //优惠券
    $router->resource('user-coupon', UserCouponController::class); //用户优惠券
    $router->resource('equity-card', EquitycardController::class); //权益卡

    $router->resource('wxminapp', WxappConfigController::class); //小程序配置

    // 设置
    $router->get('sysconfig', 'SysconfigController@index'); //开票记录
    $router->resource('banner', BannerController::class); //轮播图
    $router->resource('nav', HomeNavController::class); //首页导航图标
    $router->resource('ad', AdController::class); // 广告管理
    //$router->any('userCenter/setting', UserCenterSettingController::class); // 广告管理
    $router->resource('facilitys', OffsiteFacilityController::class); // 广告管理

    # 上传文件
    $router->any('upload/storage', 'UploadController@storage')->name('upload.storage');
    $router->any('upload/imgs', 'UploadController@imgs')->name('upload.imgs');
    $router->any('upload/files', 'UploadController@files')->name('upload.files');


    # 会员卡
    $router->resource('member-vip', MemberVipSetController::class);
    //$router->resource('member-vip-set', MemberVipSetController::class);
    $router->resource('member-order', MemberOrderController::class);
    $router->resource('member-right', MemberRightController::class);
    $router->any('user-handle/card-unavailable', 'UserHandleController@cardUnavailable');

    # 住客评价
    $router->resource('assess', AssessController::class);

    #  内容管理
    $router->resource('gonggao', GonggaoController::class); // 公告
    $router->resource('topic', TopicController::class); // 专题列表
    $router->resource('topic-type', TopicTypeController::class); // 专题分类
    $router->resource('article', ArticleController::class); // 文章列表
    $router->resource('article-type', ArticleTypeController::class); // 文章分类
    $router->resource('help', HelpController::class);// 帮助中心
    $router->resource('help-type', HelpTypeController::class);// 帮助中心 分类

    # 电子发票
    $router->resource('extend/invoice', Extend\InvoiceRegisterController::class);// 帮助中心 分类
    $router->any('extend/emailToNuonuo', 'Extend\InvoiceController@emailToNuonuo');
    $router->resource('invoices', InvoiceRegistController::class); //电子发票
    $router->resource('invoices-record', InvoicerecordController::class); //待开发票记录
    $router->get('invoices-record-history', 'InvoicerecordController@history'); //开票记录

    # 停车场
    $router->any('extend/parking', 'Extend\ParkingController@index');

    # 普通收款
    $router->resource('extend/tradeOrder', Extend\TradeOrderController::class);

    # 当面付收款
    $router->get('extend/tradepay/index', 'Extend\TradePayController@index');
    $router->post('extend/tradepay/pay', 'Extend\TradePayController@pay');

    # 活动报名
    $router->resource('extend/huodong', Extend\HuodongController::class);
    $router->resource('extend/huodong-user', Extend\HuodongUserController::class);
    $router->resource('extend/huodong-order', Extend\HuodongOrderController::class);

    // 客服中心
    $router->resource('kefu-center', KefucenterController::class);

    // 商品库
    $router->resource('goods/goodslist', GoodsWarehouse\GoodsController::class);
    $router->resource('goods/goods-cats', GoodsWarehouse\GoodsCatController::class);
    $router->resource('goods/goods-service', GoodsWarehouse\GoodsServiceController::class);
    $router->resource('goods/goods-warehouse', GoodsWarehouse\GoodsWarehouseController::class);

    // 团购商品
    $router->resource('tuangou/goods', Tuangou\TuangouGoodsController::class);
    $router->resource('tuangou/order', Tuangou\TuangouOrderController::class);
    $router->resource('tuangou/setting', Tuangou\TuangouSettingController::class);
    $router->resource('tuangou/order-comment', Tuangou\TuangouOrderCommentController::class);

    // 相册
    $router->resource('album', AlbumController::class);
    $router->resource('album-group', AlbumGroupController::class);
});


