<?php

namespace App\Api\Hotel;

use App\Models\Hotel\BalanceLog;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\RechargeOrder;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 储值
class BalanceController extends BaseController {
    public $recharge_package = [
        [
            'id'         => 1,
            'name'       => '套餐1',
            'title'      => '0.1元',
            'sub_title'  => '',
            'cost'       => 0.1,
            'give_price' => 0,
            'give_point' => 0,
        ],
        [
            'id'         => 2,
            'name'       => '套餐2',
            'title'      => '1000元',
            'sub_title'  => '',
            'cost'       => 1000,
            'give_price' => 0,
            'give_point' => 0,
        ],
        [
            'id'         => 3,
            'name'       => '套餐3',
            'title'      => '3000元',
            'sub_title'  => '',
            'cost'       => 3000,
            'give_price' => 0,
            'give_point' => 0,
        ]
    ];

    public function getRechargePackage(Request $request) {
        $user             = JWTAuth::parseToken()->authenticate();
        $hotel_id         = $request->get('hotel_id');
        $recharge_package = $this->getDiyRechargePackage($hotel_id);
        return returnData(200, 1, ['info' => $recharge_package], 'ok');
    }

    // 获取自定义的充值套餐
    public function getDiyRechargePackage($hotel_id) {
        $flds     = ['recharge_package_list'];
        $formdata = HotelSetting::getlists($flds, $hotel_id);
        //  如果有自定义的充值套餐
        $recharge_package = $this->recharge_package;
        if (!empty($formdata['recharge_package_list'])) {
            $recharge_package_list = json_decode($formdata['recharge_package_list'], true);
            $recharge_package = [];
            $iv                    = 1;
            foreach ($recharge_package_list as $key => $items) {
                $id                 = $iv;
                $recharge_package[] = [
                    'id'         => $id,
                    'name'       => '套餐' . $id,
                    'title'      => $items['cost'] . '元',
                    'sub_title'  => '送' . $items['give_price'] . '元',
                    'cost'       => $items['cost'],
                    'give_price' => $items['give_price'],
                    'give_point' => $items['give_point'],
                ];
                $iv++;
            }
        }
        return $recharge_package;
    }

    // 充值支付
    public function RechargePay(Request $request) {
        $user                = JWTAuth::parseToken()->authenticate();
        $hotel_id            = $request->get('hotel_id');
        $recharge_package_id = $request->get('recharge_package_id');
        $recharge_price      = $request->get('recharge_price');
        if (!empty($recharge_price) && !is_numeric($recharge_price)) {
            return returnData(204, 0, [], '充值金额格式不正确');
        }
        if (empty($recharge_package_id) && $recharge_price <= 0) {
            return returnData(204, 0, [], '请输入充值金额');
        }
        // 其它金额 充值
        if ($recharge_price > 0) {
            $recharge_money = $recharge_price;
            $give_price     = 0;
        }
        // 充值套餐
        $package_info = [];
        if (!empty($recharge_package_id)) {
            $recharge_package = $this->getDiyRechargePackage($hotel_id);
            foreach ($recharge_package as $key => $items) {
                if ($items['id'] == $recharge_package_id) {
                    $package_info = $items;
                }
            }
            if (empty($package_info)) {
                return returnData(204, 0, [], '未找到充值套餐信息');
            }
            $recharge_money = $package_info['cost'];
            $give_price     = $package_info['give_price'];
        }

        if (empty($recharge_money) || $recharge_money <= 0) {
            return returnData(204, 0, [], '充值金额不正确');
        }

        $subject = '会员储值卡充值';
        $openid  = $user->openid;

        // 创建订单
        $order_no = 'RE' . date('YmdHis') . str_pad(mt_rand(100, 9999), 5, '0', STR_PAD_LEFT);//'ASK' . $buyer_id . time();
        $id       = RechargeOrder::createOrder($order_no, $user->id, $hotel_id, $recharge_money, $give_price);
        return $this->ownPerPayOrderVip($order_no, $recharge_money, $subject, $openid);


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
        $isvpay     = app('wechat.isvpay');
        $config     = $isvpay->getOauthInfo('', $hotel_id);
        $app        = $isvpay->setSubMerchant($hotel_id);

        $sys_setting = \App\Models\Hotel\Setting::getlists(['isv_key']);
        if (empty($sys_setting['isv_key'])) {
            return returnData(205, 0, [], '系统支付配置错误,请联系管理员');
        }
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
        if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            return returnData(205, 0, $result, $result['return_msg']);
        }
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

    // 充值订单列表
    public function getLists(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 10);
        $where    = [
            'user_id'  => $user->id,
            'hotel_id' => $hotel_id,
        ];
        $list     = BalanceLog::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }

    // 充值订单详情
    public function getDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $id       = $request->get('id');
        $where    = [
            'id'       => $id,
            'user_id'  => $user->id,
            'hotel_id' => $hotel_id,
        ];
        $info     = BalanceLog::where($where)->first();

        return returnData(200, 1, ['info' => $info], 'ok');
    }
}
