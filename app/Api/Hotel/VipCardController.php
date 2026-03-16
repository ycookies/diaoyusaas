<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\MemberOrder;
use App\Models\Hotel\MemberRight;
use App\Models\Hotel\MemberVipSet;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Setting;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 会员卡
 */
class VipCardController extends BaseController {

    public function getCardList(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $list     = MemberVipSet::where(['hotel_id' => $hotel_id,'status' => 1])->get();
        foreach ($list as $key => &$items) {
            //$items['rights'] = MemberRights::model()->getList(['member_id'=>$items['id']]);
            $rights_lists = MemberRight::where(['member_id' => $items['id']])->get();
            $rights       = [];
            if (!empty($rights_lists)) {
                foreach ($rights_lists as $key1 => $ik1) {
                    $rights[] = [
                        'icon' => $ik1->pic_url,
                        'text' => $ik1->title,
                        'desc' => $ik1->content,
                    ];
                }
            }
            $items['rights'] = $rights;
        }
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    /**
     * 获取会员等级列表
     * @desc 获取会员等级列表
     */
    public function memberVipInfo(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $list = MemberVipSet::model()->getList(['status' => 1]);
        foreach ($list as $key => &$items) {
            //$items['rights'] = MemberRight::model()->getList(['member_id'=>$items['id']]);
            $rights_lists = MemberRight::model()->getList(['member_id' => $items['id']]);
            $rights       = [];
            if (!empty($rights_lists)) {
                foreach ($rights_lists as $key1 => $ik1) {
                    $rights[] = [
                        'icon' => $ik1['pic_url'],
                        'text' => $ik1['title'],
                        'desc' => $ik1['content'],
                    ];
                }
            }
            $items['rights'] = $rights;
        }

        return ['list' => $list];
    }

    /**
     * 购买会员
     * @desc 购买会员
     */
    public function buyMemberVip(Request $request) {
        $userInfo = JWTAuth::parseToken()->authenticate();
        $user_id  = $userInfo->id;
        $vipid    = $request->get('vipid');
        $hotel_id    = $request->get('hotel_id');
        $info     = MemberVipSet::where(['id' => $vipid])->first();
        if (empty($info)) {
            return returnData(205, 0, [], '没有找到会员卡相关信息');

        }
        // 是否已经是会员
        if ($userInfo->vipId == $vipid && count_days($userInfo->vipExpire, date('Y-m-d H:i:s')) > 30) {
            return returnData(205, 0, [], '你已经是此会员，临期30天内可续费');
        }
        $price   = $info->price;
        $subject = '购买vip:' . $info->name;
        $openid  = $userInfo->openid;
        // 创建订单
        $order_no = 'VIP' . date('YmdHis') . str_pad(mt_rand(100, 9999), 5, '0', STR_PAD_LEFT);//'ASK' . $buyer_id . time();
        $id       = MemberOrder::createOrder($order_no, $vipid, $user_id,$hotel_id, $subject);

        return $this->wxPay($hotel_id,$order_no, $price, $subject, $openid);
    }

    // 使用微信支付
    public function wxPay($hotel_id, $out_trade_no, $amount,$subject, $openid) {
        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);

        $formdata    = HotelSetting::getlists(['is_vipcard_profitsharing'], $hotel_id);
        $sys_setting = Setting::getlists(['isv_key']);
        if (empty($sys_setting['isv_key'])) {
            return returnData(205, 0, [], '系统支付配置错误,请联系管理员');
        }

        $payinfo = [
            'body'         => $subject,
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => env('APP_URL') . '/hotel/notify/wxPayNotify/' . $config->AuthorizerAppid, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'sub_openid'   => $openid,
            //'profit_sharing' => 'Y',
            //'sign_type' => 'HMAC-SHA256',
        ];

        // 是否vip会员支持分账
        if (!empty($formdata['is_vipcard_profitsharing'])) {
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

    /**
     * tudo 作废
     * @desc 走系统自己的支付 tudo
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
        // 使用自建支付 异步通知
        /*if ($app_id == 'wxedb3eb90b5566254') {
            $notify_url = 'https://asks.saishiyun.net/api/wxPayNotify/' . $app_id;
        } else {
            $notify_url = 'https://ask.dsxia.cn/api/wxPayNotify/' . $app_id;
        }*/

        $undata = [
            'body'         => $subject,
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => $notify_url, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'sub_openid'   => $openid,
        ];
        //\PhalApi\DI()->logger->info('购买会员支付参数', $undata);
        $result = $app->order->unify($undata);
        addlogs('order_unify_buy_vip',$undata,$result);
        if(!empty($result['return_code']) && $result['return_code'] == 'FAIL'){
            return returnData(205, 0, $result, $result['return_msg']);
        }
        // paySign = MD5(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111) = 22D9B4E54AB1950F51E0649E8810ACD6
        //                appId=wxf0747582a6796ddf&nonceStr=bWES8fEOD98bWSzp&package=prepay_id=wx31221528868542a2599f3c243867dc0000&signType=MD5&timeStamp=1690812928&key=YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj
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

    /**
     * 购买会员订单
     * @desc 购买会员订单
     */
    public function memberVipOrder() {

    }

}
