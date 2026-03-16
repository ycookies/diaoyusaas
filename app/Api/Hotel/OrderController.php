<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\Assess;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\BookingOrderRoomsku;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Order\Order;
use App\Models\Hotel\OrderRefund;
use App\Models\Hotel\Room;
use App\Models\Hotel\Roomprice;
use App\Models\Hotel\RoomSkuPrice;
use App\Models\Hotel\Seller;
use App\Models\Hotel\Setting;
use App\Models\Hotel\WxappConfig;
use App\Services\PriceCalculator;
use App\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends BaseController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    public function __construct() {
        $request  = Request();
        $mall_id  = $request->get('mall_id', '1');
        $hotel_id = $request->get('hotel_id');
        //$this->config = config('wechat.min2');//
        $this->config = WxappConfig::getConfig($hotel_id);
    }

    // 创建预支付订单 todo 授权模式
    public function prepayOrder(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        info($request->all());
        $request->validate(
            [
                //'open_id'       => 'required',
                //'amount'        => 'required',
                'booking_name'  => 'required',
                'booking_phone' => 'required',
                'hotel_id'      => 'required',
                'room_id'       => 'required',
                'booking_date'  => 'required',
            ],
            [
                'open_id.required'       => 'open_id不能为空',
                'amount.required'        => '总金额 不能为空',
                'booking_name.required'  => '预定人真实姓名 不能为空',
                'booking_phone.required' => '预定人联系电话 不能为空',
                'hotel_id.required'      => '酒店ID 不能为空',
                'room_id.required'       => '客房ID 不能为空',
                'booking_date.required'  => '预定日期 不能为空',
            ]
        );

        $appid        = $request->get('appid', '');
        $openid       = $request->get('open_id', '');
        $wx_mini_code = $request->get('wx_mini_code', '');
        //$amount        = $request->get('amount', 0.01);
        $booking_name  = $request->get('booking_name');
        $booking_phone = $request->get('booking_phone');
        $hotel_id      = $request->get('hotel_id');
        $room_id       = $request->get('room_id');
        $booking_date  = $request->get('booking_date');
        $booking_num   = $request->get('num', 1);
        $coupon_list   = $request->get('coupon_list');

        $pay_type          = $request->get('pay_type', 1);
        $user_pay_password = $request->get('pay_password');
        if ($pay_type == 2 && empty($user_pay_password)) {
            return returnData(205, 0, [], '请输入支付密码');
        }
        if (!is_array($booking_date)) {
            $booking_date = json_decode($booking_date, true);
        }

        $remarks = $request->get('remarks');
        if ($booking_date[0] < date('Y-m-d')) {
            return returnData(205, 0, [], '预定入驻时间不能小于今天');
        }
        $arrival_time   = !empty($booking_date[0]) ? $booking_date[0] : '';
        $departure_time = !empty($booking_date[1]) ? $booking_date[1] : '';
        if (empty($arrival_time) || empty($departure_time)) {
            return returnData(205, 0, [], '预定日期格式不正确');
        }

        // 创建预支付 订单
        $userinfo = User::where(['id' => $this->user->id])->first();
        if (empty($userinfo->openid)) {
            return returnData(205, 0, [], '未获取到用户openid');
        }
        $roominfo = Room::where(['id' => $room_id, 'hotel_id' => $hotel_id])->first();
        if (!$roominfo) {
            return returnData(205, 0, [], '未找到客房信息,请检查');
        }

        // 检查是否有剩余客房
        $is_full_room = Room::getBookingRangeIsFM($room_id, $arrival_time, $departure_time);
        if ($is_full_room !== true) {
            return returnData(205, 0, [], $is_full_room);
        }

        $hotelinfo = Seller::where(['id' => $hotel_id])->first();
        $openid    = $userinfo->openid;
        $days      = count($booking_date);

        $new_departure_time = date('Y-m-d', strtotime($departure_time . ' -1 day'));
        $days               = count_days($new_departure_time, $arrival_time);
        //$amount             = Roomprice::getRoomDateRangePrice($room_id, $arrival_time, $departure_time); // 房间的价格

        $price_lists = Roomprice::getRoomBookingRangeYouhuiPrice($this->user->id, $hotel_id, $room_id, $arrival_time, $departure_time, $booking_num);
        if (!empty($price_lists['total_price'])) {
            $amount = $price_lists['total_price'];
        }
        if (empty($amount)) {
            return returnData(205, 0, [], '获取订房总价异常,请稍候再试');
        }
        // 如果是余额支付
        if ($pay_type == 2) {
            $user         = $this->user;
            $user_balance = $user->balance;
            if (bcsub($user_balance, $amount, 2) <= 0) {
                return returnData(205, 0, [], '余额不足,无法支付');
            }

            if (empty($user->pay_password)) {
                return returnData(204, 0, [], '请先设置支付密码');
            }

            if ($user->pay_password != md5($user_pay_password)) {
                return returnData(204, 0, [], '支付密码不正确');
            }
        }

        $out_trade_no = '11' . date('YmdHis') . rand(10, 99);

        $formdata              = HotelSetting::getlists(['is_booking_profitsharing', 'booking_wait_pay_time'], $hotel_id);
        $booking_wait_pay_time = 10;
        // 订房未支付超时时间
        if (!empty($formdata['booking_wait_pay_time'])) {
            $booking_wait_pay_time = $formdata['booking_wait_pay_time'];
        }
        $pay_expire_time = date('Y-m-d H:i:s', strtotime('+' . $booking_wait_pay_time . ' minute'));
        $ins_order_data  = [
            'hotel_id'         => $roominfo->hotel_id,
            'room_id'          => $room_id,
            'user_id'          => $userinfo->id,
            'order_no'         => $out_trade_no,
            'out_trade_no'     => $out_trade_no,
            'total_cost'       => $amount,
            'price'            => $amount,
            'num'              => $booking_num,
            'days'             => $days, //
            'booking_name'     => $booking_name,
            'booking_phone'    => $booking_phone,
            'arrival_time'     => $arrival_time,    //入住时间
            'departure_time'   => $departure_time, // 离店时间
            'remarks'          => $remarks,
            'status'           => 1,
            'type'             => $pay_type,
            'code'             => rand(10000000, 99999999), // 生成核销码
            'room_type'        => $roominfo->name,
            'room_logo'        => $roominfo->logo,
            'seller_name'      => $hotelinfo->name,
            'seller_address'   => $hotelinfo->address,
            'pay_method'       => 'weixin_pay',
            'pay_expire_time'  => $pay_expire_time,
            'is_profitsharing' => isset($formdata['is_booking_profitsharing']) ? $formdata['is_booking_profitsharing'] : 0,
        ];
        $coupons_id      = '';
        if (!is_array($coupon_list)) {
            $coupon_list = json_decode($coupon_list, true);
        }
        $coupons_id_arr = collect($coupon_list)->pluck('id')->toArray();
        if ($coupons_id_arr) {
            $coupons_id = json_encode($coupons_id_arr);
            // 检查优惠券是否可用
            if (\App\Services\CouponService::checkUse($userinfo->id, $coupons_id_arr) === false) {
                return returnData(204, 0, [], '优惠券不可用,或已被使用');
            }
            // 核销优惠券
            \App\Services\CouponService::hexiao($userinfo->id, $coupons_id_arr, $out_trade_no);
        }
        if (!empty($coupons_id)) {
            $ins_order_data['coupons_id'] = $coupons_id;
        }
        $models = BookingOrder::create($ins_order_data);

        // 微信支付
        if ($pay_type == 1) {
            return $this->wxPay($hotel_id, $out_trade_no, $amount, $openid);
        }

        // 余额支付
        if ($pay_type == 2) {
            return $this->balancePay($hotel_id, $out_trade_no, $amount, $user_pay_password);
        }
        return returnData(205, 0, [], '调起支付失败');
    }

    // 创建房型sku 预支付订单 todo 授权模式
    public function prepaySkuOrder(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        info($request->all());
        $request->validate(
            [
                //'open_id'       => 'required',
                //'amount'        => 'required',
                'booking_name'  => 'required',
                'booking_phone' => 'required',
                'hotel_id'      => 'required',
                'room_id'       => 'required',
                'room_sku_id'   => 'required',
                'booking_date'  => 'required',
            ],
            [
                'open_id.required'       => 'open_id不能为空',
                'amount.required'        => '总金额 不能为空',
                'booking_name.required'  => '预定人真实姓名 不能为空',
                'booking_phone.required' => '预定人联系电话 不能为空',
                'hotel_id.required'      => '酒店ID 不能为空',
                'room_id.required'       => '房型ID 不能为空',
                'room_sku_id.required'   => '房型SKU ID 不能为空',
                'booking_date.required'  => '预定日期 不能为空',
            ]
        );

        $appid        = $request->get('appid', '');
        $openid       = $request->get('open_id', '');
        $wx_mini_code = $request->get('wx_mini_code', '');
        //$amount        = $request->get('amount', 0.01);
        $booking_name  = $request->get('booking_name');
        $booking_phone = $request->get('booking_phone');
        $hotel_id      = $request->get('hotel_id');
        $room_id       = $request->get('room_id');
        $room_sku_id   = $request->get('room_sku_id');
        $booking_date  = $request->get('booking_date');
        $booking_num   = $request->get('num', 1);
        $coupon_list   = $request->get('coupon_list');

        $pay_type          = $request->get('pay_type', 1);
        $user_pay_password = $request->get('pay_password');
        if ($pay_type == 2 && empty($user_pay_password)) {
            return returnData(205, 0, [], '请输入支付密码');
        }
        if (!is_array($booking_date)) {
            $booking_date = json_decode($booking_date, true);
        }

        $remarks = $request->get('remarks');
        if ($booking_date[0] < date('Y-m-d')) {
            return returnData(205, 0, [], '预定入驻时间不能小于今天');
        }
        $arrival_time   = !empty($booking_date[0]) ? $booking_date[0] : '';
        $departure_time = !empty($booking_date[1]) ? $booking_date[1] : '';
        if (empty($arrival_time) || empty($departure_time)) {
            return returnData(205, 0, [], '预定日期格式不正确');
        }

        // 创建预支付 订单
        $userinfo = User::where(['id' => $this->user->id])->first();
        if (empty($userinfo->openid)) {
            return returnData(205, 0, [], '未获取到用户openid');
        }
        $room_sku_info = RoomSkuPrice::where(['id' => $room_sku_id, 'hotel_id' => $hotel_id])->first();
        if (!$room_sku_info) {
            return returnData(205, 0, [], '未找到房型销售信息!');
        }
        $room_id  = $room_sku_info->room_id;
        $roominfo = Room::where(['id' => $room_id, 'hotel_id' => $hotel_id])->first();
        if (!$roominfo) {
            return returnData(205, 0, [], '未找房型信息,请检查');
        }

        // 检查是否有剩余客房
        $is_full_room = RoomSkuPrice::getBookingSkuRangeIsFM($room_sku_id, $arrival_time, $departure_time);
        if ($is_full_room !== true) {
            return returnData(205, 0, [], $is_full_room);
        }

        $hotelinfo = Seller::where(['id' => $hotel_id])->first();
        $openid    = $userinfo->openid;
        $days      = count($booking_date);

        $new_departure_time = date('Y-m-d', strtotime($departure_time . ' -1 day'));
        $days               = count_days($new_departure_time, $arrival_time);
        //$amount             = Roomprice::getRoomDateRangePrice($room_id, $arrival_time, $departure_time); // 房间的价格

        $price_lists = Roomprice::getRoomSkuBookingRangeYouhuiPrice($this->user->id, $hotel_id, $room_sku_id, $arrival_time, $departure_time, $booking_num);

        $youhui_price = !empty($price_lists['youhui_price']) ? $price_lists['youhui_price'] : 0;
        $yuan_price   = !empty($price_lists['yuan_price']) ? $price_lists['yuan_price'] : $price_lists['total_price'];
        $discounts    = $price_lists['coupon_list'];
        if (!empty($price_lists['total_price'])) {
            $total_price = $price_lists['total_price'];
        }
        if (empty($total_price)) {
            return returnData(205, 0, [], '获取订房总价异常,请稍候再试');
        }


        $amount = $total_price;
        // 如果是余额支付
        if ($pay_type == 2) {
            $user         = $this->user;
            $user_balance = $user->balance;
            if (bcsub($user_balance, $amount, 2) <= 0) {
                return returnData(205, 0, [], '余额不足,无法支付');
            }

            if (empty($user->pay_password)) {
                return returnData(204, 0, [], '请先设置支付密码');
            }

            if ($user->pay_password != md5($user_pay_password)) {
                return returnData(204, 0, [], '支付密码不正确');
            }
        }

        $out_trade_no = '11' . date('YmdHis') . rand(10, 99);

        $formdata              = HotelSetting::getlists(['is_booking_profitsharing', 'booking_wait_pay_time'], $hotel_id);
        $booking_wait_pay_time = 10;
        // 订房未支付超时时间
        if (!empty($formdata['booking_wait_pay_time'])) {
            $booking_wait_pay_time = $formdata['booking_wait_pay_time'];
        }
        $hexiaocode_prefix = \App\Models\Hotel\Order\Order::Sign_hexiaocode_prefix_RB;// 获取业务前缀
        $hexiaocode = \App\Models\Hotel\TicketsCode::getOnlyCode($hexiaocode_prefix);

        $pay_expire_time = date('Y-m-d H:i:s', strtotime('+' . $booking_wait_pay_time . ' minute'));
        $ins_order_data  = [
            'hotel_id'         => $roominfo->hotel_id,
            'room_id'          => $room_id,
            'room_sku_id'      => $room_sku_id,
            'user_id'          => $userinfo->id,
            'order_no'         => $out_trade_no,
            'out_trade_no'     => $out_trade_no,
            'total_cost'       => $amount, // 支付金额
            'price'            => $yuan_price, // 原价格
            'yhq_cost'         => $youhui_price, // 使用优惠抵扣金额
            'discount_info'    => json_encode($discounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'yyzk_cost'        => PriceCalculator::getTypeYouhuiPrice($discounts, 'yyzk_cost'),// 会员折扣金额
            'equitycard_cost'  => PriceCalculator::getTypeYouhuiPrice($discounts, 'equitycard_cost'), //权益卡优惠价格
            'dis_cost'         => PriceCalculator::getTypeYouhuiPrice($discounts, 'dis_cost'), // 优惠券的金额
            // 'yj_cost'      => '',// 押金金额
            'hb_cost'          => PriceCalculator::getTypeYouhuiPrice($discounts, 'hb_cost'),// 红包抵扣金额
            'num'              => $booking_num,
            'days'             => $days, //
            'booking_name'     => $booking_name,
            'booking_phone'    => $booking_phone,
            'arrival_time'     => $arrival_time,    //入住时间
            'departure_time'   => $departure_time, // 离店时间
            'remarks'          => $remarks,
            'status'           => 1,
            'type'             => $pay_type,
            'code'             => $hexiaocode, // 生成核销码
            'room_type'        => $roominfo->name,
            'room_logo'        => $roominfo->logo,
            'seller_name'      => $hotelinfo->name,
            'seller_address'   => $hotelinfo->address,
            'pay_method'       => 'weixin_pay',
            'pay_expire_time'  => $pay_expire_time,
            'is_profitsharing' => isset($formdata['is_booking_profitsharing']) ? $formdata['is_booking_profitsharing'] : 0,
        ];

        \DB::connection('hotel')->beginTransaction();
        try {
            $coupons_id  = '';
            $coupon_list = PriceCalculator::getTypeYouhuiInfo($discounts, 'dis_cost');
            if (!empty($coupon_list)) {
                $coupons_id_arr = [$coupon_list['coupon_id']];
                if ($coupons_id_arr) {
                    $coupons_id = json_encode($coupons_id_arr);
                    // 检查优惠券是否可用
                    if (\App\Services\CouponService::checkUse($userinfo->id, $coupons_id_arr) === false) {
                        return returnData(204, 0, [], '优惠券不可用,或已被使用');
                    }
                    // 核销优惠券
                    \App\Services\CouponService::hexiao($userinfo->id, $coupons_id_arr, $out_trade_no);
                }
                if (!empty($coupons_id)) {
                    $ins_order_data['coupons_id'] = $coupons_id;
                }
            }

            $models = BookingOrder::create($ins_order_data);

            // 备份订单对应的房型销售SKU
            BookingOrderRoomsku::add($out_trade_no, $room_sku_id);

            // 微信支付
            if ($pay_type == 1) {
                \DB::connection('hotel')->commit();
                return $this->wxPay($hotel_id, $out_trade_no, $amount, $openid);
            }

            // 余额支付
            if ($pay_type == 2) {
                \DB::connection('hotel')->commit();
                return $this->balancePay($hotel_id, $out_trade_no, $amount, $user_pay_password);
            }
        } catch (\Error $error) {
            \DB::connection('hotel')->rollBack();
            return returnData(205, 0, [], '系统异常,调起支付失败' . $error->getMessage() . '-' . $error->getLine());
        } catch (\Exception $exception) {
            \DB::connection('hotel')->rollBack();
            return returnData(205, 0, [], '系统异常,调起支付失败:' . $exception->getMessage() . '-' . $exception->getLine());
        }
        return returnData(205, 0, [], '调起支付失败');
    }

    // 使用微信支付
    public function wxPay($hotel_id, $out_trade_no, $amount, $openid) {
        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);

        $formdata    = HotelSetting::getlists(['is_booking_profitsharing'], $hotel_id);
        $sys_setting = Setting::getlists(['isv_key']);
        if (empty($sys_setting['isv_key'])) {
            return returnData(205, 0, [], '系统支付配置错误,请联系管理员');
        }

        $payinfo = [
            'body'         => '酒店客房预定',
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => env('APP_URL') . '/hotel/notify/wxPayNotify/' . $config->AuthorizerAppid, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'sub_openid'   => $openid,
            //'profit_sharing' => 'Y',
            //'sign_type' => 'HMAC-SHA256',
        ];

        // 是否支持分账
        if (!empty($formdata['is_booking_profitsharing'])) {
            $payinfo['profit_sharing'] = 'Y';
        }
        $result = $app->order->unify($payinfo);
        addlogs('order_unify', $payinfo, $result);
        if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {

            return returnData(205, 0, [], $result['return_msg']);
        }

        if (!empty($result['err_code_des'])) {
            return returnData(205, 0, [], $result['err_code_des']);
        }
        if (empty($result['prepay_id'])) {
            return returnData(205, 0, [], '创建预支付订单失败');
        }
        // paySign = MD5(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111) = 22D9B4E54AB1950F51E0649E8810ACD6
        //                appId=wxf0747582a6796ddf&nonceStr=bWES8fEOD98bWSzp&package=prepay_id=wx31221528868542a2599f3c243867dc0000&signType=MD5&timeStamp=1690812928&key=YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj
        $timestamp           = (string)time();
        $result['timeStamp'] = $timestamp;
        $sign                = 'appId=' . $config->AuthorizerAppid . '&nonceStr=' . $result['nonce_str'] . '&package=prepay_id=' . $result['prepay_id'] . '&signType=MD5&timeStamp=' . $timestamp . '&key=' . $sys_setting['isv_key'];

        //$sign = "appId=&nonceStr=&package=prepay_id=&signType=MD5&timeStamp=&key=";

        //info(['sign'=>$sign]);
        $result['paySign']  = strtoupper(MD5($sign));
        $result['nonceStr'] = $result['nonce_str'];
        $result['package']  = 'prepay_id=' . $result['prepay_id'];
        //$result['open_appid'] = 'wx662e8c427b24bdbe';
        $result['sub_mch_id'] = $config->sub_mch_id;
        //$result['signType'] = 'HMAC-SHA256';
        return returnData(200, 1, ['pay_amount' => $amount, 'pay_data' => $result, 'out_trade_no' => $out_trade_no], 'ok');
    }

    // 使用余额支付
    public function balancePay($hotel_id, $out_trade_no, $amount, $user_pay_password) {
        // 检查余额是否够用
        $user         = $this->user;
        $user_balance = $user->balance;
        if (bcsub($user_balance, $amount, 2) <= 0) {
            return returnData(205, 0, [], '余额不足,无法支付');
        }

        if (empty($user->pay_password)) {
            return returnData(204, 0, [], '请先设置支付密码');
        }

        if ($user->pay_password != md5($user_pay_password)) {
            return returnData(204, 0, [], '支付密码不正确');
        }

        // 扣除余额
        User::cutBalance($user->id, $amount, '酒店客房预定');

        // 更新订单
        $updata = [
            'status'   => 2,
            'trade_no' => '',
            'pay_time' => date('Y-m-d H:i:s'),
        ];
        BookingOrder::where(['out_trade_no' => $out_trade_no])->update($updata);

        return returnData(200, 1, ['out_trade_no' => $out_trade_no], '支付成功');
    }
    // 获取订单 订单列表

    /**
     * @desc
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * author eRic
     * dateTime 2023-08-07 10:19"id": 12,
     * "seller_id": 200014,
     * "room_id": 356,
     * "user_id": 20093,
     * out_trade_no": "YXB20230807100639",
     * "arrival_time": "2023-08-07 00:00:00",
     * "departure_time": "2023-08-07 00:00:00",
     * "total_cost": "188.00",
     * days": 1,
     * "booking_name": "杨光",
     * "booking_phone": "17681849188",
     */
    public function getBookingOrderList(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $page       = $request->get('page');
        $pagesize   = $request->get('pagesize', 20);
        $where[]    = ['user_id', '=', $this->user->id];
        $status     = $request->status;
        if ($status == 3) {
            $status = 4;
        }
        if (!empty($request->status)) {
            $where[] = ['status', '=', $status];
        }
        if (!empty($request->start_time)) {
            $where[] = ['created_at', '>=', $request->start_time];
        }
        if (!empty($request->end_time)) {
            $where[] = ['created_at', '<=', $request->end_time];
        }
        $list = BookingOrder::where($where)
            ->select(
                'id',
                'hotel_id',
                'room_id',
                'user_id',
                'out_trade_no',
                'arrival_time',
                'departure_time',
                'total_cost',
                'days',
                'booking_name',
                'booking_phone',
                'status',
                'pay_time',
                'pay_expire_time',
                'created_at',
                'remarks',
                'room_logo',
                'code',
                'is_confirm',
                'is_assess',
                'type',
                'room_type',
                'seller_name',
                'seller_address'
            )
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    public function getBookingOrderDetail(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'out_trade_no' => 'required',
            ],
            [
                'out_trade_no.required' => '订单号 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $where[]      = ['out_trade_no', '=', $out_trade_no];
        $detail       = BookingOrder::with('invoice', 'user', 'refund')->where($where)
            ->select(
                'id',
                'hotel_id',
                'room_id',
                'user_id',
                'order_no',
                'out_trade_no',
                'arrival_time',
                'departure_time',
                'room_sku_id',
                'price',
                'total_cost',
                'dis_cost',
                'yj_cost',
                'yhq_cost',
                'yyzk_cost',
                'equitycard_cost',
                'hb_cost',
                'days',
                'booking_name',
                'booking_phone',
                'status',
                'pay_time',
                'pay_expire_time',
                'trade_no',
                'created_at',
                'remarks',
                'room_logo',
                'code',
                'is_confirm',
                'is_assess',
                'type',
                'room_type',
                'seller_name',
                'seller_address'
            )->first();
        if (!$detail) {
            return returnData(204, 0, [], '未找到订单信息');
        }
        $detail->order_roomsku = [];
        if (!empty($detail->room_sku_id)) {
            $where2   = [];
            $where2[] = ['hotel_id', '=', $detail->hotel_id];
            $where2[] = ['id', '=', $detail->room_sku_id];
            $skulist  = RoomSkuPrice::where($where2)->get();
            if (!$skulist->isEmpty()) {
                foreach ($skulist as $key => &$skuinfo) {

                    //$room_sku_today_price = Roomprice::getRoomSkuDateRangePrice($skuinfo->id, $start_time, $end_time);
                    // 日期范围内的销售价
                    //$skuinfo->room_sku_today_price = $room_sku_today_price;
                    //$room_sku_today_price_arr[] = $room_sku_today_price;


                    $skuinfo->roomsku_gift_list = [];
                    $skuinfo->roomsku_tags_list = [];
                    // 礼包
                    if (!empty($skuinfo->roomsku_gift) && $skuinfo->roomsku_gift != '[]') {
                        $gift_list                  = \App\Models\Hotel\RoomSkuGift::whereIn('id', $skuinfo->roomsku_gift)->limit(1)->get();
                        $skuinfo->roomsku_gift_list = $gift_list;
                    }

                    // 标签
                    if (!empty($skuinfo->roomsku_tags_str)) {
                        $skuinfo->roomsku_tags_list = json_decode($skuinfo->roomsku_tags_str, true);
                    }
                    // 享受服务
                    if (!empty($skuinfo->roomsku_fuwu)) {
                        $skuinfo->roomsku_fuwu_arr = json_decode($skuinfo->roomsku_fuwu, true);
                    } else {
                        $skuinfo->roomsku_fuwu_arr = [];
                    }
                    //$skuinfo->roomsku_title = $skuinfo->roomsku_zaocan_title.' | '.$skuinfo->roomsku_title;
                    // 是否满房


                }
                $detail->order_roomsku = $skulist;
            }
        }
        $detail->price           = formatFloat($detail->price);
        $detail->total_cost      = formatFloat($detail->total_cost);
        $detail->dis_cost        = formatFloat($detail->dis_cost);
        $detail->yj_cost         = formatFloat($detail->yj_cost);
        $detail->yhq_cost        = formatFloat($detail->yhq_cost);
        $detail->yyzk_cost       = formatFloat($detail->yyzk_cost);
        $detail->equitycard_cost = formatFloat($detail->equitycard_cost);
        $detail->hb_cost         = formatFloat($detail->hb_cost);
        $detail->assess          = Assess::where(['order_no' => $out_trade_no])->first();
        return returnData(200, 1, ['info' => $detail], 'ok');
    }

    // 订单取消
    public function orderCancel(Request $request) {
    }

    // 订单退款
    public function orderRefund(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'hotel_id'     => 'required',
                'out_trade_no' => 'required',
                'refund_decs'  => 'required',
            ],
            [
                'hotel_id.required'     => '酒店ID 不能为空',
                'out_trade_no.required' => '订单号 不能为空',
                'refund_decs.required'  => '退款原因 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $refund_decs  = $request->get('refund_decs');
        $hotel_id     = $request->get('hotel_id');

        $where   = [];
        $where[] = ['user_id', '=', $user->id];
        $where[] = ['hotel_id', '=', $hotel_id];
        $where[] = ['out_trade_no', '=', $out_trade_no];
        $detail  = BookingOrder::where($where)
            ->select(
                'id',
                'hotel_id',
                'room_id',
                'user_id',
                'out_trade_no',
                'arrival_time',
                'departure_time',
                'total_cost',
                'days',
                'booking_name',
                'booking_phone',
                'status',
                'pay_time',
                'created_at',
                'remarks',
                'room_logo',
                'code',
                'trade_no',
                'price',
                'type',
                'room_type',
                'seller_name',
                'seller_address'
            )->first();

        if (!$detail) {
            return returnData(204, 0, [], '未找到订单信息');
        }

        // 是否满足 预定客房 退款规则
        $refund_price = $detail->total_cost;
        $fee_price    = 0;
        $canTui       = $this->cancellingQuery($request);
        if ($canTui['code'] == 200) {
            /*if (!empty($canTui['refund_price'])) { // 退款金额
                $refund_price = $canTui['refund_price'];
                $fee_price    = !empty($canTui['fee_price']) ? $canTui['fee_price'] : 0;
            }*/
            $fee_price = $refund_price;
        } else {
            // 不可退订
            return returnData(204, 0, [], $canTui['msg']);
        }

        $info = OrderRefund::where(['user_id' => $user->id, 'order_no' => $out_trade_no])->first();
        if (!empty($info->id)) {
            if ($info->status == 1) {
                return returnData(204, 0, [], '审核已经提交,请勿重复提交');
            }
            if ($info->status == 2) {
                return returnData(204, 0, [], '审核已通过,请勿再次提交');
            }
        }

        if (empty($fee_price)) {
            return returnData(207, 0, [], '不可退订取消(211)');
        }

        $out_request_no = 'R' . time();
        // 创建退款订单
        /*$config = $this->config;
        $app    = Factory::payment($config);

        $app->setSubMerchant($config['sub_mch_id']);

        $refundFee = bcmul($detail->price, 100, 0);

        $result = $app->refund->byTransactionId($detail->trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => '客房已满',
        ]);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {

            BookingOrder::where($where)->update(['status' => BookingOrder::Status7]);
            return returnData(200, 1, ['refund_id' => $result['refund_id']], '退款成功');
        }
        return returnData(205, 0, $result, '退款失败');*/

        // 改变订单状态 申请退款
        BookingOrder::where(['out_trade_no' => $out_trade_no])->update(['status' => BookingOrder::Status6]);

        // 创建预订退款订单
        $mdata      = [
            'hotel_id'       => $detail->hotel_id,
            'room_id'        => $detail->room_id,
            'user_id'        => $detail->user_id,
            'order_no'       => $detail->out_trade_no,
            'out_request_no' => $out_request_no,
            'cost'           => $refund_price,
            'fee_price'      => $fee_price,
            'refund_desc'    => $refund_decs,
            'status'         => OrderRefund::Status1,
            'sign'           => Order::Sign_HotelRoomBooking,
        ];
        $res_status = OrderRefund::firstOrCreate(['order_no' => $detail->out_trade_no], $mdata);

        $flds = [
            'is_cancelling_verify',
        ];
        // 退订是否审核
        $formdata = HotelSetting::getlists($flds, $hotel_id);
        if (!empty($formdata['is_cancelling_verify'])) {
            return returnData(200, 1, [], '退订申请已经提交');
        }

        $isvpay = app('wechat.isvpay');
        $app    = $isvpay->setSubMerchant($hotel_id);

        $refundFee = bcmul($refund_price, 100, 0);

        $result = $app->refund->byTransactionId($detail->trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => '行程变动,取消预订',
        ]);
        addlogs('refund_byTransactionId', [$detail->trade_no, $out_request_no, $refundFee, $refundFee], $result);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {
            //
            BookingOrder::where($where)->update(['status' => BookingOrder::Status7]);
            //
            OrderRefund::where(['order_no' => $detail->out_trade_no])->update(['status' => OrderRefund::Status2, 'refund_time' => date('Y-m-d H:i:s')]);
            return returnData(200, 1, ['refund_id' => $result['refund_id']], '退款成功');
        }
        $emsg = !empty($result['return_msg']) ? $result['return_msg'] : '';
        return returnData(205, 0, $result, '退款失败：' . $emsg);
    }

    // 订单核对到店
    public function orderClerk(Request $request) {
    }


    /*// 通过code 获取支付参数
    public function payWxMiniProgram(Request $request) {
        $code         = $request->get('code', '');
        $app          = Factory::miniProgram($this->config);
        $infos        = $app->auth->session($code);
        $total_amount = $request->get('total_amount', 0);
        if (!empty($infos['openid'])) {
            $openid = $infos['openid'];
            $config = $this->config;
            $payapp = Factory::payment($this->config);

            $paya = $payapp->order->unify([
                'body'         => '律鸟法律咨询服务费用',
                'out_trade_no' => 'lN' . date('YmdHis'),
                'total_fee'    => bcmul($total_amount, 100, 0),
                //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
                'notify_url'   => 'https://asks.saishiyun.net/api/min-program/wxPayNotifyWxf0747582a6796ddf', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'openid'       => $openid,
            ]);
            // paySign = MD5(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111) = 22D9B4E54AB1950F51E0649E8810ACD6
            //                appId=wxf0747582a6796ddf&nonceStr=bWES8fEOD98bWSzp&package=prepay_id=wx31221528868542a2599f3c243867dc0000&signType=MD5&timeStamp=1690812928&key=YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj
            $timestamp           = (string)time();
            $result['timestamp'] = (string)time();
            $sign                = 'appId=' . $config['app_id'] . '&nonceStr=' . $result['nonce_str'] . '&package=prepay_id=' . $result['prepay_id'] . '&signType=MD5&timeStamp=' . $timestamp . '&key=' . $config['key'];
            $result['paySign']   = MD5($sign);
            $result['sign_str']  = $sign;
            return returnData(200, 1, $result, 'ok');
        }

        return returnData(205, 0, [], 'fail');
    }

    // 调用喜大收银台地址
    public function preOrders(Request $request) {
        info($request->all());
        $appid        = $request->get('appid', '');
        $open_id      = $request->get('open_id', '');
        $total_amount = $request->get('total_amount', '');
        $channel_id   = $request->get('channel_id', '');

        $cpaydata = [
            //'mod'          => 'center',
            //'timestamp' => date('Y-m-d H:i:s'),
            // 'channel_id'   => '100131',
            'channel_id'   => $channel_id,
            'user_id'      => '1002938', // 出售者的用户ID
            'order_no'     => 'lv' . time(),
            'total_amount' => $total_amount,
            'scene'        => 1,
            'pid'          => 'wx',
            'subject'      => '律鸟-咨询服务付费',
            //'return_url'   => 'https://asks.saishiyun.net/api/min-program/wxPayNotifyWxf0747582a6796ddf',
            'open_id'      => $open_id
        ];
        $service  = new \App\Services\SignerService();
        $payurl   = $service->prePayPam($cpaydata);
        $res      = HttpsCurl($payurl, [], true);
        addlogs('preOrders', $request->all(), $res, 0);
        $res  = json_decode($res, true);
        $ress = [];
        if (!empty($res['data']['pay_data'])) {
            $ress           = json_decode($res['data']['pay_data'], true);
            $ress['pay_no'] = $res['data']['pay_no'];
            return returnData(200, 1, $ress, 'ok');
        }

        return returnData(205, 0, $ress, '支付遇到问题');
    }*/

    // 创建预支付订单 todo 自研模式
    public function prepayOrderOld(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                //'open_id'       => 'required',
                //'amount'        => 'required',
                'booking_name'  => 'required',
                'booking_phone' => 'required',
                'hotel_id'      => 'required',
                'room_id'       => 'required',
                'booking_date'  => 'required',
            ],
            [
                'open_id.required'       => 'open_id不能为空',
                'amount.required'        => '总金额 不能为空',
                'booking_name.required'  => '预定人真实姓名 不能为空',
                'booking_phone.required' => '预定人联系电话 不能为空',
                'hotel_id.required'      => '酒店ID 不能为空',
                'room_id.required'       => '客房ID 不能为空',
                'booking_date.required'  => '预定日期 不能为空',
            ]
        );

        $appid        = $request->get('appid', '');
        $openid       = $request->get('open_id', '');
        $wx_mini_code = $request->get('wx_mini_code', '');
        //$amount        = $request->get('amount', 0.01);
        $booking_name  = $request->get('booking_name');
        $booking_phone = $request->get('booking_phone');
        $hotel_id      = $request->get('hotel_id');
        $room_id       = $request->get('room_id');
        $booking_date  = $request->get('booking_date');
        if (!is_array($booking_date)) {
            $booking_date = json_decode($booking_date, true);
        }

        $remarks = $request->get('remarks');
        if ($booking_date[0] < date('Y-m-d')) {
            return returnData(205, 0, [], '预定入驻时间不能小于今天');
        }

        $config = $this->config;
        $app    = Factory::payment($config);
        /*if (!empty($wx_mini_code)) {
            $miniProgram = Factory::miniProgram($this->config);
            $infos       = $miniProgram->auth->session($wx_mini_code);
            if (empty($infos['openid'])) {
                return returnData(205, 0, [], '支付异常(openid)');
            }
            $openid = $infos['openid'];
        }*/
        // 创建预支付 订单
        $userinfo = User::where(['id' => $this->user->id])->first();
        if (empty($userinfo->openid)) {
            return returnData(205, 0, [], '未获取到用户openid');
        }
        $roominfo  = Room::where(['id' => $room_id])->first();
        $hotelinfo = Seller::where(['id' => $hotel_id])->first();
        $openid    = $userinfo->openid;
        $days      = count($booking_date);
        if ($days = 1) {
            $arrival_time   = $booking_date[0];
            $departure_time = $booking_date[1];
        } else {
            $arrival_time   = $booking_date[0];
            $departure_time = array_pop($booking_date);
        }
        $amount         = Roomprice::getRoomDateRangePrice($room_id, $booking_date[0], $booking_date[1]); // 房间的价格
        $out_trade_no   = '11' . date('YmdHis') . rand(10, 99);
        $ins_order_data = [
            'hotel_id'       => $roominfo->hotel_id,
            'room_id'        => $room_id,
            'user_id'        => $userinfo->id,
            'order_no'       => $out_trade_no,
            'out_trade_no'   => $out_trade_no,
            'total_cost'     => $amount,
            'price'          => $amount,
            'days'           => $days, //
            'booking_name'   => $booking_name,
            'booking_phone'  => $booking_phone,
            'arrival_time'   => $arrival_time,    //入住时间
            'departure_time' => $departure_time, // 离店时间
            'remarks'        => $remarks,
            'status'         => 1,
            'type'           => 1,
            'code'           => rand(10000000, 99999999), // 生成核销码
            'room_type'      => $roominfo->name,
            'room_logo'      => $roominfo->logo,
            'seller_name'    => $hotelinfo->name,
            'seller_address' => $hotelinfo->address,
            'pay_method'     => 'weixin_pay',

        ];
        $models         = BookingOrder::create($ins_order_data);
        // 测试账号专用
        if ($userinfo->id == 20124) {
            $amount = 0.1;
        }
        $payinfo = [
            'body'         => '酒店客房预定',
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => env('APP_URL') . '/hotel/notify/wxPayNotify/' . $this->config['app_id'], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'       => $openid,
        ];
        $app->setSubMerchant($config['sub_mch_id']);
        $result = $app->order->unify($payinfo);
        if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            return returnData(205, 0, [], $result['return_msg']);
        }

        if (!empty($result['err_code_des'])) {
            return returnData(205, 0, [], $result['err_code_des']);
        }
        if (empty($result['prepay_id'])) {
            return returnData(205, 0, [], '创建预支付订单失败');
        }
        // paySign = MD5(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111) = 22D9B4E54AB1950F51E0649E8810ACD6
        //                appId=wxf0747582a6796ddf&nonceStr=bWES8fEOD98bWSzp&package=prepay_id=wx31221528868542a2599f3c243867dc0000&signType=MD5&timeStamp=1690812928&key=YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj
        $timestamp           = (string)time();
        $result['timeStamp'] = (string)time();
        $sign                = 'appId=' . $config['app_id'] . '&nonceStr=' . $result['nonce_str'] . '&package=prepay_id=' . $result['prepay_id'] . '&signType=MD5&timeStamp=' . $timestamp . '&key=' . $config['key'];
        $result['paySign']   = MD5($sign);
        $result['nonceStr']  = $result['nonce_str'];
        $result['package']   = 'prepay_id=' . $result['prepay_id'];
        return returnData(200, 1, ['pay_data' => $result, 'out_trade_no' => $out_trade_no], 'ok');


        /*$jssdk = $payment->jssdk;
        $config = $jssdk->bridgeConfig($prepayId, false);*/
    }

    // 是否 满足退订规则
    public function cancellingQuery(Request $request) {
        $user         = JWTAuth::parseToken()->authenticate();
        $out_trade_no = $request->get('out_trade_no');
        $refund_decs  = $request->get('refund_decs');
        $hotel_id     = $request->get('hotel_id');
        // 获取退订规则
        $flds     = [
            'booking_full_status',
            'cancelling_time',
            'exceed_cancelling_time_rate_24',
            'exceed_cancelling_time_rate_48',
            'vip_cancelling_time',
            'vip_exceed_cancelling_time_rate_24',
            'vip_exceed_cancelling_time_rate_48',
        ];
        $formdata = HotelSetting::getlists($flds, $hotel_id);

        // 是否是vip
        $vip_status = !empty($user->vipId) ? true : false;
        // 是vip
        if ($vip_status) {
            // 无限制预订取消
            if ($formdata['vip_cancelling_time'] == 0) {
                return ['code' => 200, 'msg' => 'ok'];
            }
            // 不可退订取消
            if ($formdata['vip_cancelling_time'] == '1') {
                return ['code' => 403, 'msg' => '不可退订取消(vip规则)'];
            }
        }

        // 无限制预订取消
        if ($formdata['cancelling_time'] == 0) {
            return ['code' => 200, ['' => ''], 'msg' => 'ok'];
        }
        // 不可退订取消
        if ($formdata['cancelling_time'] == '1') {
            return ['code' => 403, 'msg' => '不可退订取消(订房规则)'];
        }
    }
}
