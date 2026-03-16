<?php

namespace App\Api\Portal;

use App\Admin;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\Room;
use App\Models\Hotel\Seller;
use App\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends BaseController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;

    public function __construct() {
        $request       = Request();
        $mall_id       = $request->get('mall_id', '1');
        $this->config  = config('wechat.min' . $mall_id);
        $this->mall_id = $mall_id;
    }


    // 创建预支付订单
    public function prepayOrder(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                //'open_id'       => 'required',
                'amount'        => 'required',
                'booking_name'  => 'required',
                'booking_phone' => 'required',
                'hotel_id'      => 'required',
                'room_id'       => 'required',
                'booking_date'  => 'required',
            ], [
                'open_id.required'       => 'open_id不能为空',
                'amount.required'        => '总金额 不能为空',
                'booking_name.required'  => '预定人真实姓名 不能为空',
                'booking_phone.required' => '预定人联系电话 不能为空',
                'hotel_id.required'      => '酒店ID 不能为空',
                'room_id.required'       => '客房ID 不能为空',
                'booking_date.required'  => '预定日期 不能为空',
            ]
        );

        $appid         = $request->get('appid', '');
        $openid        = $request->get('open_id', '');
        $wx_mini_code  = $request->get('wx_mini_code', '');
        $amount        = 0.01;// $request->get('amount', 0.01);
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
        info($request->all());
        $config = $this->config;
        $app    = Factory::payment($config);
        if (!empty($wx_mini_code)) {
            $miniProgram = Factory::miniProgram($this->config);
            $infos       = $miniProgram->auth->session($wx_mini_code);
            if (empty($infos['openid'])) {
                return returnData(205, 0, [], '支付异常(openid)');
            }
            $openid = $infos['openid'];
        }
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
            $departure_time = $booking_date[0];
        } else {
            $arrival_time   = $booking_date[0];
            $departure_time = array_pop($booking_date);
        }

        $out_trade_no   = 'YXB' . date('YmdHis');
        $ins_order_data = [
            'seller_id'      => $roominfo->seller_id,
            'room_id'        => $room_id,
            'user_id'        => $userinfo->id,
            'out_trade_no'   => $out_trade_no,
            'total_cost'     => $roominfo->price,
            'price'          => $amount,
            'days'           => $days, //
            'booking_name'   => $booking_name,
            'booking_phone'  => $booking_phone,
            'arrival_time'   => $arrival_time,    //入住时间
            'departure_time' => $departure_time, // 离店时间
            'remarks'        => $remarks,
            'status'         => 1,
            'type'           => 1,
            'code'           => $userinfo->id . 'TO' . Str::random(6), // 生成核销码
            'room_type'      => $roominfo->name,
            'room_logo'      => $roominfo->logo,
            'seller_name'    => $hotelinfo->name,
            'seller_address' => $hotelinfo->address,

        ];
        $models         = BookingOrder::create($ins_order_data);

        $result = $app->order->unify([
            'body'         => '酒店客房预定',
            'out_trade_no' => $out_trade_no,
            'total_fee'    => bcmul($amount, 100, 0),
            //'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url'   => env('APP_URL') . '/portalapi/notify/wxPayNotifyWx8c3d9b0bbf9272bc', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'       => $openid,
        ]);
        if (!empty($result['err_code_des'])) {
            return returnData(205, 0, [], $result['err_code_des']);
        }
        // paySign = MD5(appId=wxd678efh567hg6787&nonceStr=5K8264ILTKCH16CQ2502SI8ZNMTM67VS&package=prepay_id=wx2017033010242291fcfe0db70013231072&signType=MD5&timeStamp=1490840662&key=qazwsxedcrfvtgbyhnujmikolp111111) = 22D9B4E54AB1950F51E0649E8810ACD6
        //                appId=wxf0747582a6796ddf&nonceStr=bWES8fEOD98bWSzp&package=prepay_id=wx31221528868542a2599f3c243867dc0000&signType=MD5&timeStamp=1690812928&key=YKAr9V0IdHl4kvs0CMQ2DTVluSZROlYj
        $timestamp           = (string)time();
        $result['timeStamp'] = (string)time();
        $sign                = 'appId=' . $config['app_id'] . '&nonceStr=' . $result['nonce_str'] . '&package=prepay_id=' . $result['prepay_id'] . '&signType=MD5&timeStamp=' . $timestamp . '&key=' . $config['key'];
        $result['paySign']   = MD5($sign);
        $result['sign_str']  = $sign;
        $result['package']   = 'prepay_id=' . $result['prepay_id'];
        return returnData(200, 1, ['pay_data' => $result], 'ok');


        /*$jssdk = $payment->jssdk;
        $config = $jssdk->bridgeConfig($prepayId, false);*/
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
        $where[] = ['user_id','=',$this->user->id];
        if(!empty($request->status)){
            $where[] = ['status','=',$request->status];
        }
        if(!empty($request->start_time)){
            $where[] = ['created_at','>=',$request->start_time];
        }
        if(!empty($request->end_time)){
            $where[] = ['created_at','<=',$request->end_time];
        }
        $list       = BookingOrder::where($where)
            ->select(
                'id', 'seller_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code',
                'room_type', 'seller_name', 'seller_address'
            )
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);
        return returnData(200, 1, $this->pageintes($list), 'ok');

    }

    public function getBookingOrderDetail(Request $request) {
        $this->user = JWTAuth::parseToken()->authenticate();
        $request->validate(
            [
                'out_trade_no'        => 'required',
            ], [
                'out_trade_no.required'       => '订单号 不能为空',
            ]
        );
        $out_trade_no       = $request->get('out_trade_no');
        $where[] = ['out_trade_no','=',$out_trade_no];
        $detail = BookingOrder::where($where)
            ->select(
                'id', 'seller_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        return returnData(200, 1, ['info'=> $detail], 'ok');

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
}
