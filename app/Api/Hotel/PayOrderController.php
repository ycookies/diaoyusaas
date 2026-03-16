<?php

namespace App\Api\Hotel;

use App\Admin;
use App\User;
use App\Models\Hotel\BookingOrder;
use App\Services\RongbaoPayService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Hotel\UserRongbaopayOrder;

// 获取融宝支付系统的订单业务
class PayOrderController extends BaseController {
    public $user;

    // 根据openid 获取订单列表
    public function getPayisvLists(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $pay_type = $request->get('pay_type', 'wx');
        $openid   = $user->getRongbaopayOpenid($hotel_id, $pay_type);
        $page     = $request->get('page', 1);
        $pagesize = $request->get('pagesize', 10);

        $isvpay  = app('wechat.isvpay');
        $config  = $isvpay->getOauthInfo('', $hotel_id);
        $service = new RongbaoPayService();
        $res     = $service->getOrderByOpenID($openid, $config['sub_mch_id'], $page, $pagesize);
        addlogs('RongbaoPay_getOrderByOpenID', ['openid' => $openid], $res, $user->id);
        $res_arr = json_decode($res, true);
        /*if (empty($res_arr['data'])) {
            return returnData(204, 0, [], '未找到订单信息');
        }*/

        $data['list']      = !empty($res_arr['data']) ? $res_arr['data'] : [];
        $data['page_info'] = [
            'page'     => $page,
            'pagesize' => $pagesize,
            'total'    => 10,
        ];
        return returnData(200, 1, $data, 'ok');
    }

    // 根据订单号获取订单详情
    public function getDetail(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Error $error) {
            return returnData(403, 0, [], '未登陆');
        } catch (\Exception $exception) {
            return returnData(403, 0, [], '未登陆');
        }
        //$openid_info = $user->getRongbaopayOpen();
        $hotel_id = $request->get('hotel_id');

        $request->validate(
            [
                'out_trade_no' => 'required',
            ], [
                'out_trade_no.required' => '订单号 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $where[]      = ['out_trade_no', '=', $out_trade_no];
        $detail       = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code', 'is_confirm', 'is_assess',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        if (!$detail) {
            // 去融宝支付系统查询
            $paymenttype_arr  = [
                'wx'     => '微信支付',
                'alipay' => '支付宝支付',
            ];
            $payment_user_arr = [
                'wx'     => 'buyeruserid',
                'alipay' => 'alipaypid',
            ];
            $service          = new RongbaoPayService();
            $res              = $service->getOrderDetail($out_trade_no);
            addlogs('RongbaoPay_getOrderDetail', ['out_trade_no' => $out_trade_no], $res, $user->id);
            $res_arr = json_decode($res, true);
            if (!empty($res_arr['data']['tradeno'])) {
                $detail                    = $res_arr['data'];
                $detail['paymenttype_txt'] = !empty($paymenttype_arr[$detail['paymenttype']]) ? $paymenttype_arr[$detail['paymenttype']] : '-';
                //$openid_field              = !empty($payment_user_arr[$detail['paymenttype']]) ? $payment_user_arr[$detail['paymenttype']] : '';
                $detail['openid']          = '-';
                if (!empty($detail['buyeruserid'])) {
                    $detail['openid'] = $detail['buyeruserid'];
                }
                $detail['source'] = 'payisv';
                $detail['total_cost'] = !empty($detail['money'])? $detail['money']:0;
                $detail['out_trade_no'] = !empty($detail['outtradeno'])? $detail['outtradeno']:'-';
                $detail['created_at'] = !empty($detail['time'])? $detail['time']:'-';
            }
        } else {
            $detail = $detail->toArray();
            if ($detail['user_id'] != $user->id) {
                return returnData(204, 0, [], '他人订单,你无权限查看');
            }
            $detail['source'] = 'minapp';
        }

        /*if ($detail['source'] == 'payisv') {
            $status = $user->verifyRongbaopayOpenid($hotel_id, $detail['paymenttype'], $detail['openid']);
            if ($status !== true) {
                return returnData(204, 0, [], $status);
            }
        }*/
        if (!$detail) {
            return returnData(204, 0, [], '未找到订单信息');
        }
        return returnData(200, 1, ['info' => $detail], 'ok');

    }


    // 根据融宝pos机二维码 获取订单详情
    public function getQrocdeOrderDetail(Request $request) {
        //$openid_info = $user->getRongbaopayOpen();
        info('根据融宝pos机二维码',$request->all());
        $hotel_id = $request->get('hotel_id');
        $request->validate(
            [
                'wx_code'      => 'required',
                'out_trade_no' => 'required',

            ], [
                'wx_code.required'      => '微信Code 不能为空',
                'out_trade_no.required' => '订单号 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        if($out_trade_no == 'undefined' && $request->has('orderurl') && $request->get('orderurl') != 'undefined'){
            $out_trade_no_str = str_replace(env('APP_URL').'/qr/','',$request->get('orderurl'));
            $out_trade_no_arr = explode('/',$out_trade_no_str);
            if(count($out_trade_no_arr) >1 ){
                $out_trade_no = $out_trade_no_arr[1];
            }else{
                $out_trade_no = $out_trade_no_arr[0];
            }
            info('根据融宝pos机-orderurl',[$out_trade_no,$request->get('orderurl')]);
        }

        if(strpos($out_trade_no,'wb') !== false || strpos($out_trade_no,'ab') !== false){
            $out_trade_no = substr($out_trade_no, 4);
        }
        $wx_code      = $request->get('wx_code');
        // 查看wx_code 是否已经注册 如果没有注册，则注册
        $uid = UserService::checkUserRegister($wx_code,$hotel_id,true);

        $where[] = ['out_trade_no', '=', $out_trade_no];
        $detail  = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code', 'is_confirm', 'is_assess',
                'room_type', 'seller_name', 'seller_address'
            )->first();
        //
        $logdata = [
            'user_id' => $uid,
            'hotel_id'=> $hotel_id,
            'out_trade_no' => $out_trade_no,
            'source'=> 'minapp',
        ];
        if (!$detail) {
            //
            $logdata['source'] = 'payisv';

            // 去融宝支付系统查询
            $paymenttype_arr  = [
                'wx'     => '微信支付',
                'alipay' => '支付宝支付',
            ];
            $payment_user_arr = [
                'wx'     => 'buyeruserid',
                'alipay' => 'alipaypid',
            ];
            $service          = new RongbaoPayService();
            $res              = $service->getOrderDetail($out_trade_no);
            addlogs('RongbaoPay_getOrderDetail', ['out_trade_no' => $out_trade_no], $res);
            $res_arr = json_decode($res, true);
            if (!empty($res_arr['data']['tradeno'])) {
                $detail                    = $res_arr['data'];
                $detail['paymenttype_txt'] = !empty($paymenttype_arr[$detail['paymenttype']]) ? $paymenttype_arr[$detail['paymenttype']] : '-';
                $detail['openid']          = '-';
                if (!empty($detail['buyeruserid'])) {
                    $detail['openid'] = $detail['buyeruserid'];
                }
                $detail['source'] = 'payisv';
            }
        } else {
            $detail = $detail->toArray();
            if ($detail['user_id'] != $uid) {
                // return returnData(204, 0, [], '他人订单,你无权限查看');
            }
            $detail['source'] = 'minapp';
        }
        // 记录查看日志
        $logdata['openid'] = !empty($detail['openid']) ? $detail['openid']:'-';
        UserRongbaopayOrder::store($logdata);

        if (!empty($detail['source']) && $detail['source'] == 'payisv') {
            if ($uid !== false) {
                // 验证是否被他人绑定，如果没有形成绑定关系
                $status = (new UserService())->verifyRongbaopayOpenid($hotel_id, $uid, $detail['paymenttype'], $detail['openid']);
                if ($status !== true) {
                    // return returnData(204, 0, [], $status); //已被他人绑定
                }
            }
        }
        if (!$detail) {
            return returnData(204, 0, [], '未找到订单信息');
        }


        $detail['userinfo'] = User::where(['id'=> $uid])->select('id','name','nick_name','card_code')->first();
        return returnData(200, 1, ['info' => $detail], 'ok');

    }


}
