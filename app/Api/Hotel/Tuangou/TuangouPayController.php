<?php

namespace App\Api\Hotel\Tuangou;

use App\Api\Hotel\BaseController;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Order\Order;
use App\Models\Hotel\Order\OrderDetail;
use App\Models\Hotel\Setting;
use App\Models\Hotel\Tuangou\TuangouGoods;
use App\Models\Hotel\Tuangou\TuangouOrder;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use App\Services\PriceCalculator;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use WeChatPay\Formatter;
use WeChatPay\Crypto\Rsa;
// 团购订单支付
class TuangouPayController extends BaseController {

    // 订单支付
    public function orderPay(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'hotel_id'         => 'required',
                'tuangou_goods_id' => 'required',
                'pay_type'         => 'required',
                'goods_num'        => 'required',
            ],
            [
                'hotel_id.required'         => '酒店ID 不能为空',
                'tuangou_goods_id.required' => '团购商品 不能为空',
                'pay_type.required'         => '请选择付费方式 不能为空',
                'goods_num'                 => '团购商品数量 不能为空',
            ]
        );
        $tuangou_goods_id   = $request->get('tuangou_goods_id');
        $hotel_id           = $request->get('hotel_id');
        $remark             = $request->get('remark');
        $goods_num          = $request->get('goods_num');
        $tuangou_goods_info = TuangouGoods::with('goods', 'warehouse')
            ->where([
                'id'       => $tuangou_goods_id,
                'hotel_id' => $hotel_id,
            ])
            ->first();
        if (!$tuangou_goods_info) {
            return returnData(404, 0, [], '找不到团购商品信息');
        }
        $pay_type          = $request->get('pay_type', 1);
        $user_pay_password = $request->get('pay_password');
        if ($pay_type == 2 && empty($user_pay_password)) {
            return returnData(205, 0, [], '请输入支付密码');
        }

        $user_id     = $this->user->id;
        $total_price = bcmul($tuangou_goods_info->goods->price,$goods_num,2); //原金额
        $amount = $total_price;
        $discounts = [];
        $youhui_price = 0;

        /*$calculator = new PriceCalculator($total_price);
        $calculator->calculateFinalPrice($user_id);

        // 获取最终价格和折扣信息
        $amount       = $calculator->getFinalPrice(); // 最后支付金额
        $youhui_price = $calculator->getYouhuiPrice();
        $discounts    = $calculator->getDiscounts();*/

        // 创建预支付 订单
        $userinfo = User::where(['id' => $this->user->id])->first();
        if (empty($userinfo->openid)) {
            return returnData(205, 0, [], '未获取到用户openid');
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
        $openid   = $userinfo->openid;
        $order_no = '21' . date('YmdHis') . rand(10, 99);

        do {
            // 生成6位随机数
            $offline_qrcode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 检查是否存在，不存在则退出循环
            $exists = Order::where([['offline_qrcode', '=', $offline_qrcode], ['clerk_id', '<>', null]])->exists();
        } while ($exists);

        $formdata = HotelSetting::getlists(['is_booking_profitsharing'], $hotel_id);
        // 生成基础订单
        $ins_order_data = [
            'hotel_id'                   => $tuangou_goods_info->hotel_id,
            'user_id'                    => $this->user->id,
            'order_no'                   => $order_no,
            'is_pay'                     => Order::Is_pay_0,
            'pay_type'                   => $pay_type,
            'total_price'                => $total_price,
            'total_pay_price'            => $amount,
            'send_type'                  => Order::Send_type_1,
            'offline_qrcode'             => $offline_qrcode,
            'sign'                       => Order::Sign_tuangou,
            'is_comment'                 => Order::Is_comment_0,
            'status'                     => Order::Status_0,
            'remark'                     => $remark,
            'total_goods_price'          => $amount, // 订单商品总金额(优惠后)
            'total_goods_original_price' => $total_price, // 订单商品总金额(优惠前)
            'coupon_discount_price'      => PriceCalculator::getTypeYouhuiPrice($discounts, 'dis_cost'), // 优惠券的金额
            'discount_info'              => json_encode($discounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'is_profitsharing'           => isset($formdata['is_booking_profitsharing']) ? $formdata['is_booking_profitsharing'] : 0,

        ];
        $order_info     = Order::create($ins_order_data);

        // 订单详情
        $ins_orderDetail_data = [
            'order_id'             => $order_info->id,
            'goods_id'             => $tuangou_goods_info->goods_id,
            'num'                  => $goods_num,
            'unit_price'           => $tuangou_goods_info->goods->price,
            'total_original_price' => $tuangou_goods_info->goods->price,
            'total_price'          => $tuangou_goods_info->goods->price,
            'goods_info'           => json_encode(collect($tuangou_goods_info)->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'sign'                 => Order::Sign_tuangou,
        ];
        OrderDetail::create($ins_orderDetail_data);

        // 生成拼团信息
        $ins_TuangouOrder_data = [
            'hotel_id'           => $tuangou_goods_info->hotel_id,
            'preferential_price' => 0,
            'success_time'       => date('Y-m-d H:i:s'),
            'status'             => 2, // 拼团成功
            'people_num'         => 1,//一人团
            'pintuan_time'       => 0,// 拼团限时(小时)
            'goods_id'           => $tuangou_goods_info->goods_id,
        ];
        $TuangouOrder_info     = TuangouOrder::create($ins_TuangouOrder_data);


        // 生成团购订单
        $ins_TuangouOrderRelation_data = [
            'hotel_id'         => $tuangou_goods_info->hotel_id,
            'order_id'         => $order_info->id,
            'user_id'          => $this->user->id,
            'pintuan_order_id' => $TuangouOrder_info->id,
            'is_parent'        => 1,
            'is_groups'        => 0, // 单独购买
            'order_status'     => 1, // 待付款
        ];
        TuangouOrderRelation::create($ins_TuangouOrderRelation_data);

        // 微信支付
        if ($pay_type == 1) {
            return $this->wxPay($hotel_id, $order_no, $amount, $openid);
        }

        // 余额支付
        if ($pay_type == 2) {
            return $this->balancePay($hotel_id, $order_no, $amount, $user_pay_password);
        }
        return returnData(205, 0, [], '调起支付失败');

    }

    // 使用微信支付
    public function wxPay($hotel_id, $out_trade_no, $amount, $openid) {
        $isvpay = app('wechat.isvpay');
        $wechatpay_config = config('wechat.min2');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);

        // 获取分账配置
        //$formdata    = HotelSetting::getlists(['is_tuangou_profitsharing'], $hotel_id);
        $formdata    = HotelSetting::getlists(['is_booking_profitsharing'], $hotel_id);
        $sys_setting = Setting::getlists(['isv_key']);
        if (empty($sys_setting['isv_key'])) {
            return returnData(205, 0, [], '系统支付配置错误,请联系管理员');
        }

        $payinfo = [
            'body'         => '团购服务商品',
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
        $pay_instance = (new \App\libary\WeChatPay\WeChatPay())->makePay();

        /*// ************ 使用微信支付V3 ************
        $pay_total = intval(bcmul($amount, 100, 0));
        if($pay_total <= 0){
            return returnData(205, 0, [], '支付金额错误！');
        }
        $pay_instance = (new \App\libary\WeChatPay\WeChatPay())->makePay();
        $pay_param = [
            'sp_appid' => $wechatpay_config['app_id'],
            'sp_mchid'=>  $wechatpay_config['mch_id'],
            'sub_appid' => $config->AuthorizerAppid,
            'sub_mchid' => $config->sub_mch_id,
            'description' => '团购服务商品',
            'out_trade_no' => $out_trade_no,
            'notify_url'   => env('APP_URL') . '/hotel/notify/wxPayNotifyV3/' . $config->AuthorizerAppid, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'amount'       => [
                'total' => $pay_total, // 金额（分）
                'currency' => 'CNY'
            ],
            'payer' => [
                'sub_openid'=> $openid,
            ],
        ];
        // 是否支持分账
        if (!empty($formdata['is_tuangou_profitsharing'])) {
            $pay_param['settle_info'] = [
                'profit_sharing' => true,
            ];
        }

        $err_msg = '';
        try {
            $resp = $pay_instance->chain('v3/pay/partner/transactions/jsapi')
            ->post([
                'json'    => $pay_param,
                'headers' => [
                    'Accept' => 'application/json',
                    'Wechatpay-Serial' => $wechatpay_config['platform_pub_id'],
                ]
            ]);
            $res_arr = json_decode($resp->getBody()->getContents(),true);
            addlogs('order_unify', $pay_param, $res_arr);
            if(empty($res_arr['prepay_id'])){
                return returnData(205, 0, [], '支付错误:未获取到支付参数');
            }
            $params = [
                'appId'     => $config->AuthorizerAppid,
                'timeStamp' => (string)Formatter::timestamp(),
                'nonceStr'  => Formatter::nonce(),
                'package'   => 'prepay_id='.$res_arr['prepay_id'],
            ];
            $merchantPrivateKeyFilePath = 'file://' . $wechatpay_config['key_path'];
            $merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath);

            $params  = array_merge($params,['paySign' => Rsa::sign(
                Formatter::joinedByLineFeed(...array_values($params)),
                $merchantPrivateKeyInstance
            ), 'signType' => 'RSA']);
            $params['sub_mch_id'] = $config->sub_mch_id;
            return returnData(200, 1, ['pay_amount' => $amount, 'pay_data' => $params, 'out_trade_no' => $out_trade_no], 'ok');

        } catch (\Error $error) {
            $err_msg =  $error->getMessage();
        } catch (\Exception $exception) {
            $err_msg =  $exception->getMessage();
        }
        info('支付错误:'.$err_msg);
        return returnData(205, 0, [], '支付错误:'.$err_msg);*/

        // ************ end 使用微信支付V3 *************/

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
        $result['signType'] = 'MD5';
        $result['sub_mch_id'] = $config->sub_mch_id;
        //$result['signType'] = 'HMAC-SHA256';
        return returnData(200, 1, ['pay_amount' => $amount, 'pay_data' => $result, 'out_trade_no' => $out_trade_no], 'ok');
    }

    // 使用余额支付
    public function balancePay($hotel_id, $order_no, $amount, $user_pay_password) {
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
        User::cutBalance($user->id, $amount, '团购商品支付费用');

        // 更新订单
        $updata = [
            'pay_type' => 3,
            'is_pay'   => 1,
            'trade_no' => '',
            'pay_time' => date('Y-m-d H:i:s'),
        ];
        Order::where(['order_no' => $order_no])->update($updata);

        return returnData(200, 1, ['order_no' => $order_no], '支付成功');
    }

    // 团购订单退款
    public function orderRefund(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $order_no = $request->get('order_no');
        $order    = Order::where(['order_no' => $order_no])->first();
        if (empty($order->trade_no)) {
            return returnData(205, 0, [], '非法操作.订单未支付');
        }
        /*if(!empty($order->clerk_id)){
            return returnData(205, 0, [], '订单已核销,无法线上退款');
        }*/
        $trade_no = $order->trade_no;
        $isvpay   = app('wechat.isvpay');
        $app      = $isvpay->setSubMerchant($hotel_id);

        $refundFee = bcmul($order->total_pay_price, 100, 0);
        $result    = $app->refund->byTransactionId($trade_no, $order_no, $refundFee, $refundFee, [
            'refund_desc' => '行程变动,取消团购',
        ]);
        addlogs('refund_byTransactionId', [$trade_no, $order_no, $refundFee, $refundFee], $result);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {
            //
            //BookingOrder::where($where)->update(['status' => BookingOrder::Status7]);
            //
            //OrderRefund::where(['order_no' => $detail->out_trade_no])->update(['status' => OrderRefund::Status2]);
            return returnData(200, 1, ['refund_id' => $result['refund_id']], '退款成功');
        }
        $emsg = !empty($result['return_msg']) ? $result['return_msg'] : '';
        return returnData(205, 0, $result, '退款失败：' . $emsg);

    }

    // 获取团购价格
    public function getTuangouPayTotalPrice(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'hotel_id'         => 'required',
                'tuangou_goods_id' => 'required',
            ],
            [
                'hotel_id.required'         => '酒店ID 不能为空',
                'tuangou_goods_id.required' => '团购商品 不能为空',
                'goods_num'                 => '商品数量不能为空',
            ]
        );
        $tuangou_goods_id   = $request->get('tuangou_goods_id');
        $hotel_id           = $request->get('hotel_id');
        $goods_num          = $request->get('goods_num', 1);
        $tuangou_goods_info = TuangouGoods::with('goods', 'warehouse')
            ->where([
                'id'       => $tuangou_goods_id,
                'hotel_id' => $hotel_id,
            ])
            ->first();
        if (!$tuangou_goods_info) {
            return returnData(404, 0, [], '找不到团购商品信息');
        }

        $total_price = bcmul($tuangou_goods_info->goods->price, $goods_num, 2);
        $total_price = formatFloat($total_price);

        /*$user_id = $this->user->id;
        $calculator = new \App\Services\PriceCalculator($total_price);
        $calculator->calculateFinalPrice($user_id);

        // 获取最终价格和折扣信息
        $finalPrice = $calculator->getFinalPrice();
        $youhui_price = $calculator->getYouhuiPrice();
        $discounts = $calculator->getDiscounts();*/

        /*$data        = [
            'youhui_price' => $youhui_price,
            'yuan_price'   => $total_price,
            'total_price'  => $finalPrice,
            'coupon_list'  => $discounts,
        ];*/

        $data        = [
            'youhui_price' => '',
            'yuan_price'   => $total_price,
            'total_price'  => $total_price,
            'coupon_list'  => [],
        ];
        return returnData(200, 1, $data, 'ok');
    }

}
