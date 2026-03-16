<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\UserLevelUpOrder;
use App\Models\Hotel\UserLevelRight;
use App\Models\Hotel\MemberVipSet;
use App\Models\Hotel\UserLevel;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * 会员等级
 */
class UserLevelController extends BaseController {

    public function getLevelList(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $where = [
            ['hotel_id','=',$hotel_id],
            ['level_num','<>',0]
        ];
        $list     = UserLevel::with('rights')->where($where)->orderBy('level_num','ASC')->get();
        /*foreach ($list as $key => &$items) {
            //$items['rights'] = MemberRights::model()->getList(['member_id'=>$items['id']]);
            $rights_lists = UserLevelRight::where(['hotel_id'=>$hotel_id,'level_id' => $items['id']])->get();
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
        }*/
        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 用户等级详情
    public function getLevelDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $level_id = $user->level_id;
        $info     = UserLevel::with('rights')->where(['id'=>$level_id,'hotel_id' => $hotel_id])->first();
        return returnData(200, 1, ['info'=>$info], 'ok');
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
     * 付费提升等级
     * @desc 付费提升等级
     */
    public function buyUpUserlevel(Request $request) {
        $userInfo = JWTAuth::parseToken()->authenticate();
        $user_id  = $userInfo->id;
        info($request->all());
        $level_id    = $request->get('level_id');
        $hotel_id    = $request->get('hotel_id');
        $info     = UserLevel::where(['id' => $level_id,'hotel_id'=> $hotel_id])->first();
        if (empty($info)) {
            return returnData(205, 0, [], '没有找到会员卡相关信息');

        }
        // 是否已经是会员
        if ($userInfo->level_id == $level_id) {
            return returnData(205, 0, [], '你已经是此会员，不必提升升级');
        }
        $price   = $info->buy_price;
        $subject = '付费提升会员等级:' . $info->level_name;
        $openid  = $userInfo->openid;
        // 创建订单
        $order_no = 'LV' . date('YmdHis') . str_pad(mt_rand(100, 9999), 5, '0', STR_PAD_LEFT);//'ASK' . $buyer_id . time();
        $id       = UserLevelUpOrder::createOrder($order_no, $level_id, $user_id,$hotel_id, $subject);

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
