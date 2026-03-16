<?php

namespace App\Api\Hotel;

use App\Models\Hotel\ParkingOrder;
use App\Services\ParkingService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 停车场
class ParkingController extends BaseController {

    // 获取用户历史停车记录
    public function getUserCarLists(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $wx_code     = $request->get('wx_code');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        // 通过code 获取用户信息
        $userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);
        // 微信获取失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }
        $user_id = $userinfo->id;
        $where    = [
            'user_id'  => $user_id,
            'hotel_id' => $hotel_id,
            'pay_status' => 1,
        ];
        $list     = ParkingOrder::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 获取用户历史停车详情
    public function getUserCarDetail(Request $request) {
        $user       = JWTAuth::parseToken()->authenticate();
        $hotel_id   = $request->get('hotel_id');
        $outTradeNo = $request->get('outTradeNo');

        $info = ParkingOrder::where(['user_id' => $user->id, 'outTradeNo' => $outTradeNo])
            ->first();

        return returnData(200, 1, ['info' => $info], 'ok');
    }

    // 查询临时停车缴费金额
    public function queryCarFee(Request $request) {
        info($request->all());

        $hotel_id = $request->get('hotel_id');
        $carNo    = $request->get('carNo');
        if (empty($carNo)) {
            return returnData(204, 0, [], '请输入车牌号码');
        }
        $service  = new ParkingService($hotel_id);
        $postdata = [
            'carNo' => $carNo,
        ];
        //$res      = $service->sendapi('yunpark/thirdInterface/getCarFee', $postdata);
        //$res      = json_decode($res, true);
        $res_json = '{"success":true,"message":"success","code":200,"timestamp":1659341416230,"result":{"parkingNo":"P1700624069382","carNo":"新A6H74C","openId":"123456","carType":11,"chargeTime":"2022-08-01 14:31:19","endChargeTime":"2022-08-01 16:10:16","totalAmount":1,"disAmount":0,"couponAmount":1,"mac":"574bfaeb-d3a361a5"}}';
        $res = json_decode($res_json, true);
        if (empty($res['result']['carNo'])) {
            return returnData(204, 0, [], '未查询到车辆入场信息');
        }
        $res['result']['carNo'] = $carNo;
        $res['result']['totalAmount'] = bcdiv($res['result']['totalAmount'],100,2);
        return returnData(200, 1, ['info' => $res['result']], 'ok');
    }

    // 支付停车费用
    public function payCarCost(Request $request) {
        $hotel_id = $request->get('hotel_id');
        $carNo    = $request->get('carNo');
        $wx_code  = $request->get('wx_code');
        $request->validate(
            [
                'hotel_id' => 'required',
                'wx_code'  => 'required',
                'carNo'    => 'required',
                //'remarks'  => 'required',
            ], [
                'hotel_id.required' => '酒店ID 不能为空',
                'wx_code.required'  => '微信code 不能为空',
                'carNo.required'    => '请输入车牌号码',
            ]
        );
        $service  = new ParkingService($hotel_id);
        $postdata = [
            'carNo' => $carNo,
        ];
        /*$res      = $service->sendapi('yunpark/thirdInterface/getCarFee', $postdata);
        $res      = json_decode($res, true);
        if (empty($res['result']['carNo'])) {
            return returnData(204, 0, [], '未查询到车辆入场信息');
        }
        if (empty($res['result']['totalAmount'])) {
            return returnData(204, 0, [], '无需缴费,免费出行');
        }*/

        $res_json = '{"success":true,"message":"success","code":200,"timestamp":1659341416230,"result":{"parkingNo":"P1700624069382","carNo":"新A6H74C","openId":"123456","carType":11,"chargeTime":"2022-08-01 14:31:19","endChargeTime":"2022-08-01 16:10:16","totalAmount":800,"disAmount":0,"couponAmount":800,"mac":"574bfaeb-d3a361a5"}}';
        $res = json_decode($res_json, true);

        // 通过code 获取用户信息
        $userinfo = \App\Services\UserService::wxcodeGetUserinfo($wx_code, $hotel_id);
        // 微信登陆失败
        if (empty($userinfo->id)) {
            $errormsg = '获取微信用户openid失败';
            return returnData(204, 0, [], $errormsg);
        }

        $carinfo    = $res['result'];
        $carinfo['chargeTime'] = date('Y-m-d 08:20:32');
        $carinfo['endChargeTime'] = date('Y-m-d 12:20:32');
        $carinfo['totalAmount'] = 1;
        $outTradeNo = 'PARK' . $hotel_id . date('YmdHis');
        $amount     = bcdiv($carinfo['totalAmount'],100,2);
        $subject    = '缴纳停车费';
        $openid     = $userinfo->openid;

        // 创建订单
        /**
         * hotel_id 酒店ID
         * user_id  用户ID
         * parkingNo 停车场编号
         * outTradeNo 商家订单编号
         * transactionId 交易流水编号
         * carNo 车辆牌号
         * chargeTime 收费时间
         * endChargeTime 结束计费时间
         * payType 支付类型
         * couponAmount 实收金额
         * disAmount 打折金额
         * endChargeTime  支付完成时间
         * mac 通道mac地址
         * openid 微信openid
         * totalAmount 应收金额
         */
        $insdata = [
            'hotel_id'      => $hotel_id,
            'user_id'       => $userinfo->id,
            'parkingNo'     => $service->getParkingNo(),
            'outTradeNo'    => $outTradeNo,
            'carNo'         => $carinfo['carNo'],
            'chargeTime'    => $carinfo['chargeTime'],
            'endChargeTime' => $carinfo['endChargeTime'],
            'payType'       => 1,
            'couponAmount'  => $amount,
            'disAmount'     => 0,
            'totalAmount'   => $amount,
            'openid'        => $openid,
        ];
        ParkingOrder::addOrder($insdata);
        return $this->ownPerPayOrderVip($outTradeNo, $amount, $subject, $openid);

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
