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
# 微信各类通知
Route::any('/notify/wxPayNotifyV3/{id}', 'WxPayNotifyV3Controller@wxPayNotify'); //v3支付通知验签
Route::any('/notify/wxPayNotify/{id}', 'NotifyController@wxPayNotify');
Route::any('/notify/oauthNotify', 'OauthController@oauthNotify');
Route::any('/notify/{id}/callbackOpen', 'CallbackController@callbackOpen');
Route::any('/notify/callbackTest', 'DemoController@callbackTest');
Route::any('/notify/invoiceNotify', 'InvoiceNotifyController@notify'); // 电票异步通知
Route::any('/gzhnotify', 'GzhNotifyController@notify');

# 分账动账通知
Route::any('/notify/profitsharing', 'ProfitsharingNotifyController@notify');

Route::any('/other/xieyi', 'IndexController@getXieyi');

Route::group(['middleware' => ['hotelapi']], function () {
    Route::any('index', 'IndexController@index');
    Route::any('index/getPhoneCode', 'IndexController@getPhoneCode');
    Route::post('index/jisuanJuli', 'IndexController@jisuanJuli');
    Route::post('index/getPhoneNumber', 'IndexController@getPhoneNumber'); // 小程序获取手机号
    Route::any('index/getShopConfig', 'IndexController@getShopConfig');
    /*Route::any('banner/lists', 'IndexController@banner');
    Route::any('nav/lists', 'IndexController@nav');*/

    # 上传图片/文件
    Route::post('uploads/upimg', 'UploadsController@upimg');
    Route::post('uploads/upfile', 'UploadsController@upfile');

    Route::any('hotel/lists', 'HotelController@lists');
    Route::any('detail', 'HotelController@detail');
    Route::any('infos', 'HotelController@infos');
    Route::any('getTemplateMsgID', 'HotelController@getTemplateMsgID');

    # 小工具
    Route::any('mintools/getQrcode', 'MinToolsController@getQrcode');

    # 信客评价
    Route::any('assess/getHoteAssessList', 'AssessController@getHoteAssessList');

    # 文章 专题 帮助
    Route::any('article/cats', 'ArticleController@getCats');
    Route::any('article/lists', 'ArticleController@getLists');
    Route::any('article/detail', 'ArticleController@getDetail');

    Route::any('topic/cats', 'TopicController@getCats');
    Route::any('topic/lists', 'TopicController@getLists');
    Route::any('topic/detail', 'TopicController@getDetail');

    Route::any('help/cats', 'HelpController@getCats');
    Route::any('help/lists', 'HelpController@getLists');
    Route::any('help/detail', 'HelpController@getDetail');

    # 客房
    Route::any('/room/lists', 'RoomController@lists');
    Route::any('/room/detail', 'RoomController@detail');

    # 房型销售SKU
    Route::any('/roomsku/getRoomLists', 'RoomSkuController@getRoomLists');
    Route::any('/roomsku/getRoomDetail', 'RoomSkuController@getRoomDetail');
    Route::any('/roomsku/lists', 'RoomSkuController@lists');

    # 小超市
    Route::any('/smg/cats', 'SmgController@cats');
    Route::any('/smg/lists', 'SmgController@lists');
    Route::any('/smg/detail', 'SmgController@detail');

    # 停车场
    Route::any('/parking/getUserCarLists', 'ParkingController@getUserCarLists');
    Route::any('/parking/getUserCarDetail', 'ParkingController@getUserCarDetail');
    Route::post('/parking/queryCarFee', 'ParkingController@queryCarFee');
    Route::post('/parking/payCarCost', 'ParkingController@payCarCost');

    # 小程序收款
    Route::post('/qrcode_pay/trade', 'TradeOrderController@trade');
    Route::any('/qrcode_pay/getTradeorderLists', 'TradeOrderController@getTradeorderLists');
    Route::any('/qrcode_pay/getTradeorderDetail', 'TradeOrderController@getTradeorderDetail');



    Route::any('user/wxUserLoginOrRegiser', 'UserController@wxUserLoginOrRegiser');


    Route::any('user/getAddCardParam', 'UserController@getAddCardParam');

    # 扫码融宝pos机二维码 获取订单详情
    Route::any('/payorder/getQrocdeOrderDetail', 'PayOrderController@getQrocdeOrderDetail');


    # 用户登陆 获取token
    Route::any('user/wxlogin', 'UserController@wxlogin');
    Route::any('user/checkUserRegister', 'UserController@checkUserRegister');

    # 活动信息
    Route::any('/huodong/getLists', 'HuodongController@getLists');
    Route::any('/huodong/getDetail', 'HuodongController@getDetail');

    # 团购商品信息
    Route::any('/tuangou/getGoodsList', 'Tuangou\TuangouGoodsController@goodsList');
    Route::any('/tuangou/getGoodsDetial', 'Tuangou\TuangouGoodsController@goodsDetail');

    # 全局评价
    Route::any('/fullorder/commentList', 'Order\OrderCommentController@commentList');
    Route::any('/fullorder/commentDetail', 'Order\OrderCommentController@commentDetail');

    # 相册
    Route::get('/album/list', 'AlbumController@getLists');

    # 需要 token
    Route::group(['middleware' => ['hotelauthapi']], function () {
        Route::any('user/getUserinfo', 'UserController@getUserinfo');
        Route::any('user/bindPhone', 'UserController@bindPhone');
        Route::post('user/upLocationInfo', 'UserController@upLocationInfo');
        Route::post('user/upUserProfile', 'UserController@upUserProfile');
        Route::post('user/setPayPassword', 'UserController@setPayPassword');


        # 住宿评价
        Route::post('assess/addAssess', 'AssessController@addAssess');
        Route::post('assess/editAssess', 'AssessController@editAssess');


        # 微信卡券
        Route::post('user/applyMember', 'UserController@applyMember');

        # 客房预定 订单
        Route::any('getBookingOrderList', 'OrderController@getBookingOrderList');
        Route::any('getBookingOrderDetail', 'OrderController@getBookingOrderDetail');
        Route::any('/payorder/getPayisvLists', 'PayOrderController@getPayisvLists'); // 查询融宝支付系统交易订单
        Route::any('/payorder/getDetail', 'PayOrderController@getDetail');

        # 获取预定日期总费用
        Route::any('/room/getBookingRangeTotalPrice', 'RoomController@getBookingRangeTotalPrice');
        Route::any('/room/getRoomCalendarPrice', 'RoomController@getRoomCalendarPrice');

        # 房型sku销售
        Route::any('/roomsku/detail', 'RoomSkuController@detail');
        Route::any('/roomsku/getSkuBookingRangeTotalPrice', 'RoomSkuController@getSkuBookingRangeTotalPrice');
        Route::any('/roomsku/getRoomSkuCalendarPrice', 'RoomSkuController@getRoomSkuCalendarPrice');


        # 酒店预定
        Route::post('/booking/preorder', 'OrderController@prepayOrder');
        Route::post('/booking/preSkuOrder', 'OrderController@prepaySkuOrder'); // 房型销售sku订单
        Route::post('/booking/order/refund', 'OrderController@orderRefund');

        # 会员等级
        Route::any('/userlevel/getLevelList', 'UserLevelController@getLevelList');
        Route::any('/userlevel/getLevelDetail', 'UserLevelController@getLevelDetail');
        Route::any('/userlevel/buyUpUserlevel', 'UserLevelController@buyUpUserlevel');


        # 会员卡
        Route::any('/uservip/MemberVipInfo', 'VipCardController@getCardList');
        Route::any('/uservip/BuyMemberVip', 'VipCardController@buyMemberVip');
        Route::any('/uservip/MemberVipOrder', 'VipCardController@memberVipOrder');

        # 常用信息
        Route::any('/commonifo/getLvkeinfoList', 'CommonInfoController@getLvkeinfoList');
        Route::any('/commonifo/getLvkeinfoDefault', 'CommonInfoController@getLvkeinfoDefault');
        Route::any('/commonifo/getLvkeinfoDetail', 'CommonInfoController@getLvkeinfoDetail');
        Route::post('/commonifo/actionLvkeinfo', 'CommonInfoController@actionLvkeinfo');

        # 优惠券
        Route::any('/coupon/getLists', 'CouponController@getLists');
        Route::any('/coupon/getUserLists', 'CouponController@getUserLists');
        Route::post('/coupon/receive', 'CouponController@receive'); // 领取优惠券
        Route::post('/coupon/minappFavSendConpon', 'CouponController@minappFavSendConpon'); // 收藏小程序 发放优惠券活动

        # 邀请分享
        Route::any('/share/getShareConfig', 'ShareController@getShareConfig');
        Route::any('/share/getShareUserLists', 'ShareController@getShareUserLists');
        Route::any('/share/getSharePosterQrcode', 'ShareController@getSharePosterQrcode');

        # 储值
        Route::any('/balance/getRechargePackage', 'BalanceController@getRechargePackage');
        Route::any('/balance/RechargePay', 'BalanceController@RechargePay');
        Route::any('/balance/getLists', 'BalanceController@getLists');
        Route::any('/balance/getDetail', 'BalanceController@getDetail');

        # 资金
        Route::any('/finance/getFinanceLists', 'FinanceController@getFinanceLists');
        Route::any('/finance/getFinanceDetail', 'FinanceController@getFinanceDetail');

        # 积分
        Route::any('/point/getPointLists', 'PointController@getPointLists');
        Route::any('/point/getPointDetail', 'PointController@getPointDetail');


        # 发票
        Route::any('/invoice/getLists', 'InvoiceController@getLists');
        Route::any('/invoice/getDetail', 'InvoiceController@getDetail');
        Route::post('/invoice/invoicing', 'InvoiceController@invoicing');

        // 用户参入活动
        Route::post('/huodong/baomingSave', 'HuodongController@baomingSave');
        Route::get('/huodong/getUserHuodong', 'HuodongController@getUserHuodong');
        Route::get('/huodong/getUserHuodongOrder', 'HuodongController@getUserHuodongOrder');

        # 用户评价列表
        Route::any('assess/getUserAssessList', 'AssessController@getUserAssessList');

        # 全局订单
        Route::post('/fullorder/getGoodsPayPriceInfo', 'Goods\GoodsController@getGoodsPayPriceInfo');
        Route::post('/fullorder/applyOrderRefund', 'Order\OrderController@applyOrderRefund');
        Route::any('/fullorder/getOrderList', 'Order\OrderController@orderList');
        Route::any('/fullorder/getOrderDetail', 'Order\OrderController@orderDetail');
        Route::post('/fullorder/addComment', 'Order\OrderCommentController@addComment');
        Route::post('/fullorder/editComment', 'Order\OrderCommentController@editComment');

        # 团购订单
        Route::post('/tuangou/orderPay', 'Tuangou\TuangouPayController@orderPay');
        Route::post('/tuangou/getTuangouPayTotalPrice', 'Tuangou\TuangouPayController@getTuangouPayTotalPrice');
        Route::post('/tuangou/orderRefund', 'Tuangou\TuangouPayController@orderRefund');
        Route::any('/tuangou/getOrderList', 'Tuangou\TuangouOrderController@orderList');
        Route::any('/tuangou/getOrderDetail', 'Tuangou\TuangouOrderController@orderDetail');


    });
});
