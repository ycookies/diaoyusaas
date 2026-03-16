<?php

namespace App\Api\Hotel;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\HotelSetting;
use Illuminate\Support\Arr;

// 小程序收款
class QrcodePayController extends BaseController {

    public function qrcodePay(Request $request) {
        $user          = JWTAuth::parseToken()->authenticate();
        $hotel_id      = $request->get('hotel_id');
        $recharge_package_id = $request->get('recharge_package_id');
        $recharge_package = $this->recharge_package;
        Arr::get($recharge_package,'id','');

        $price   = $info->price;
        $subject = '购买vip:' . $info->name;
        $openid  = $userInfo->openid;
        // 创建订单
        $order_no = 'VIP' . date('YmdHis') . str_pad(mt_rand(100, 9999), 5, '0', STR_PAD_LEFT);//'ASK' . $buyer_id . time();
        $id       = MemberOrder::createOrder($order_no, $vipid, $user_id,$hotel_id, $subject);

        return $this->ownPerPayOrderVip($order_no, $price, $subject, $openid);
    }

    /**
     * @desc 走系统自己的支付
     * author eRic
     * dateTime 2023-10-30 20:58
     */
    public function ownPerPayOrderVip($out_trade_no, $amount, $subject, $openid) {
        $request  = Request();
        $hotel_id = $request->get('hotel_id');
        $app_id   = $request->get('app_id');
        /*$app_id = requests('app_id');
        $config = $this->getMinAppConfig($app_id);
        $payapp = Factory::payment($config);*/
        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);
        $notify_url = env('APP_URL') . '/hotel/notify/wxPayNotify/' . $app_id;

        $undata = [
            'body'         => $subject,
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => $notify_url, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'sub_openid'   => $openid,
        ];
        $result = $app->order->unify($undata);
        if(!empty($result['return_code']) && $result['return_code'] == 'FAIL'){
            return returnData(205, 0, $result, $result['return_msg']);
        }
        $timestamp           = (string)time();
        $result['timeStamp'] = (string)time();
        $sign                = 'appId=' . $app_id . '&nonceStr=' . $result['nonce_str'] . '&package=prepay_id=' . $result['prepay_id'] . '&signType=MD5&timeStamp=' . $timestamp . '&key=' . $config['apikey'];
        $result['paySign']   = MD5($sign);
        $result['package']   = 'prepay_id=' . $result['prepay_id'];
        $result['nonceStr']  = $result['nonce_str'];
        $result['sign_str']  = $sign;
        $paydata =  ['pay_data' => $result, 'order_no' => $out_trade_no];

        return returnData(200, 1, $paydata, 'ok');
    }

    // 收款订单列表
    public function getQrcodePayOrderLists(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);

        $list     = User::where(['temp_parent_id' => $user->id])
            ->orWhere(['parent_id' => $user->id])
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 收款订单详情
    public function getQrcodePayOrderDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);

        $list     = User::where(['temp_parent_id' => $user->id])
            ->orWhere(['parent_id' => $user->id])
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }
}
