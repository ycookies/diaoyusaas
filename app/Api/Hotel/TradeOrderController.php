<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\TradeOrder;
use Illuminate\Http\Request;

// 小程序在线收款
class TradeOrderController extends BaseController {
    public $user;

    // 获取历史交易列表
    public function getTradeorderLists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page',1);
        $pagesize = $request->get('pagesize', 10);
        $wx_code  = $request->get('wx_code');
        $request->validate(
            [
                'hotel_id' => 'required',
                'wx_code'  => 'required',
                //'remarks'  => 'required',
            ], [
                'hotel_id.required' => '酒店ID 不能为空',
                'wx_code.required'  => '微信code 不能为空',
            ]
        );
        // 通过code 获取用户信息
        $userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);
        // 微信获取失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }
        $user_id = $userinfo->id;
        $where   = [
            'user_id'  => $user_id,
            'hotel_id' => $hotel_id,
            'pay_status' => 1,// 只获取支付成功的
        ];
        $list    = TradeOrder::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取历史交易列表
    public function getTradeorderDetail(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $out_trade_no  = $request->get('out_trade_no');
        $request->validate(
            [
                'hotel_id' => 'required',
                'out_trade_no'  => 'required',
                //'remarks'  => 'required',
            ], [
                'hotel_id.required' => '酒店ID 不能为空',
                'out_trade_no.required'  => '订单编号 不能为空',
            ]
        );
        $where   = [
            'hotel_id' => $hotel_id,
            'out_trade_no' => $out_trade_no,
        ];
        $info    = TradeOrder::where($where)->first();

        return returnData(200, 1, ['info'=>$info], 'ok');
    }

    // 正常收款
    public function trade(Request $request) {
        $request->validate(
            [
                'hotel_id' => 'required',
                'wx_code'  => 'required',
                'amount'   => 'required',
                //'remarks'  => 'required',
            ], [
                'hotel_id.required' => '酒店ID 不能为空',
                'wx_code.required'  => '微信code 不能为空',
                'amount.required'   => '金额不能为空 不能为空',
                'remarks.required'  => '退款原因 不能为空',
            ]
        );
        $hotel_id = $request->get('hotel_id');
        $wx_code  = $request->get('wx_code');
        $amount   = $request->get('amount');
        $remarks  = $request->get('remarks');

        $regx = '/^[0-9]+(.[0-9]{2})?$/'; // 最多两位小数的正整数
        // 检查数据合法性
        if (!preg_match($regx, $amount)) {
            return returnData(204, 0, [], '金额 最多带两位小数');
        }
        // 通过code 获取用户信息
        $userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);

        // 微信登陆失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }
        $wx_openid    = $userinfo->openid;
        $out_trade_no = 'TR' . $hotel_id . date('YmmHis');
        $subject      = '消费收款';
        $user_id      = $userinfo->id;

        // 组装数据
        $insdata = [
            'hotel_id'      => $hotel_id,
            'user_id'       => $user_id,
            'out_trade_no'  => $out_trade_no,
            'type'          => TradeOrder::Type_101,//
            'fina_type'     => 'in',
            'pay_ways'      => 'wxpay',
            'total_amount' => $amount,
            'real_amount'   => $amount,
            'pay_status'    => 0,
            'remarks'       => $remarks,
        ];
        TradeOrder::addOrder($insdata);
        return $this->ownPerPayOrder($out_trade_no, $amount, $subject, $wx_openid);

    }

    /**
     * @desc 走系统自己的支付
     * author eRic
     * dateTime 2023-10-30 20:58
     */
    public function ownPerPayOrder($out_trade_no, $amount, $subject, $openid) {
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
        if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
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
        $paydata             = ['pay_data' => $result, 'order_no' => $out_trade_no];

        return returnData(200, 1, $paydata, 'ok');
    }

}
