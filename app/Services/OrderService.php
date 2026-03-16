<?php

namespace App\Services;

use App\Models\Hotel\Order\Order;
use App\Models\Hotel\Order\OrderDetail;
use App\Models\Hotel\OrderRefund;
use App\Models\Hotel\ProfitsharingOrder;
use App\Models\Hotel\ProfitsharingOrderReceiver;
use App\Models\Hotel\ProfitsharingReceiver;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use App\Models\Hotel\HotelSetting;
/**
 * 订单服务
 * @package App\Services
 * anthor Fox
 */
class OrderService extends BaseService {

    // 订单退款
    public function orderRefund($out_trade_no, $refund_decs) {
        /*$request->validate(
            [
                'out_trade_no' => 'required',
                'refund_decs'  => 'required',
            ], [
                'out_trade_no.required' => '订单号 不能为空',
                'refund_decs.required'  => '退款原因 不能为空',
            ]
        );
        $out_trade_no = $request->get('out_trade_no');
        $refund_decs  = $request->get('refund_decs');*/
        $info = OrderRefund::where(['order_no' => $out_trade_no])->first();
        if (!empty($info->id)) {
            if ($info->status == 1) {
                return returnData(204, 0, [], '审核已经提交,请勿重复提交');
            }
            if ($info->status == 2) {
                return returnData(204, 0, [], '审核已通过,请勿再次提交');
            }
        }

        // 是否满足 预定客房 退款规则
        $refund_config = true;
        if ($refund_config) {

        }


        $where[]        = ['out_trade_no', '=', $out_trade_no];
        $detail         = BookingOrder::where($where)
            ->select(
                'id', 'hotel_id', 'room_id', 'user_id', 'out_trade_no',
                'arrival_time', 'departure_time', 'total_cost', 'days', 'booking_name',
                'booking_phone', 'status', 'pay_time', 'created_at', 'remarks', 'room_logo',
                'code', 'trade_no', 'price',
                'room_type', 'seller_name', 'seller_address'
            )->first();
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

        // 创建退款订单

        $mdata = [
            'hotel_id'       => $detail->hotel_id,
            'room_id'        => $detail->room_id,
            'user_id'        => $detail->user_id,
            'order_no'       => $detail->out_trade_no,
            'out_request_no' => $out_request_no,
            'cost'           => $detail->total_cost,
            'refund_desc'    => $refund_decs,
            'status'         => OrderRefund::Status1,
        ];

        OrderRefund::firstOrCreate(['order_no' => $detail->out_trade_no], $mdata);

        $refund_order = [
            'uid'        => $detail->user_id,
            'refund_no'  => $out_request_no,
            'order_no'   => $detail->out_trade_no,
            'mode'       => 'wx',
            'status'     => '1',
            'total_fee'  => $detail->total_cost,
            'refund_fee' => $detail->price,
        ];
        $res_status   = Refund::firstOrCreate(['order_no' => $detail->out_trade_no], $refund_order);

        if ($res_status) {
            return returnData(200, 1, [], '退款申请已经提交');
        }


        $app    = wechatPay($detail->hotel_id);
        $config = \App\Models\Hotel\WxappConfig::getConfig($detail->hotel_id);
        $app->setSubMerchant($config['sub_mch_id']);

        $refundFee = bcmul($detail->price, 100, 0);

        $result = $app->refund->byTransactionId($detail->trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => '客房已满',
        ]);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {

            BookingOrder::where($where)->update(['status' => BookingOrder::Status7]);
            return returnData(200, 1, ['refund_id' => $result['refund_id']], '退款成功');
        }
        return returnData(205, 0, $result, '退款失败');
    }

    // 开始分账 todo 测试用例
    public function profitsharingOld($out_trade_no) {
        $where[] = ['out_trade_no', '=', $out_trade_no];
        $detail  = BookingOrder::where($where)->first();

        //$config = WxappConfig::getConfig('143');

        //$app    = Factory::payment($config);

        //$app->setSubMerchant($config['sub_mch_id']);
        $app = app('wechat.isvpay')->setSubMerchant($detail->hotel_id);

        /*$receiver     = [
            "type"          => "PERSONAL_OPENID",
            "account"       => "oADNc5FhR1QNRVdgNlN_JnriHul4",//PERSONAL_OPENID：个人openid
            "name"          => "杨永光",//接收方真实姓名
            "relation_type" => "PARTNER"
        ];*/

        $receiver = [
            "type"          => "MERCHANT_ID",
            "account"       => "1566291601",//PERSONAL_OPENID：个人openid
            "name"          => "深圳市融宝科技有限公司",//接收方真实姓名
            "relation_type" => "SERVICE_PROVIDER"
        ];


        /*$mk           = $app->profit_sharing->addReceiver($receiver);
        echo "<pre>";
        print_r($mk);
        echo "</pre>";
        exit;*/

        $out_trade_no = "SH" . time();
        $receivers    = [
            [
                "type"        => "MERCHANT_ID",
                "account"     => "1566291601",
                "amount"      => 20,
                "description" => "分到服务商"
            ]
        ];
        info([$detail->trade_no, $out_trade_no]);
        $sharing = $app->profit_sharing->share($detail->trade_no, $out_trade_no, $receivers);
        echo "<pre>";
        print_r([$sharing, $detail->toArray(), $out_trade_no]);
        echo "</pre>";
        exit;
    }

    // 开始分账
    public function profitsharing($order_no) {
        $where[]   = ['order_no', '=', $order_no];
        $orderinfo = Order::where($where)->first();

        if (empty($orderinfo->is_profitsharing)) {
            return '订单不支持交易分账';
        }

        // 查看是否有分账接受方
        $rece = ProfitsharingReceiver::where(['hotel_id' => $orderinfo->hotel_id])->get();
        if (!$rece) {
            return '此商家没有分账接受方';
        }
        $profitsharing_no = "PF" . $orderinfo->hotel_id . time();
        info('分账订单号:', [$profitsharing_no]);

        // 计算参于分账人数和分账支出总金额
        $profitsharing_price = ProfitsharingReceiver::jisuanProfitsharingOutMoney($orderinfo->total_pay_price, $rece);
        if (empty($profitsharing_price['total_money'])) {
            return '分账总支出为0,无法分账';
        }
        // 组装分账接受方数组
        $receivers_arr = [];
        foreach ($rece as $key => $itmm) {
            $receiver_money_list = $profitsharing_price['receiver_money_list'];
            $amount              = $receiver_money_list[$itmm->account];
            $receivers_arr[]     = [
                "type"        => $itmm->type,
                "account"     => $itmm->account,
                "amount"      => bcmul($amount, 100, 0),
                "description" => "分到" . ProfitsharingReceiver::Relation_type_arr[$itmm->relation_type]
            ];
        }
        // 创建分账订单
        $insdata = [
            'hotel_id'                        => $orderinfo->hotel_id,
            'profitsharing_no'                => $profitsharing_no,
            'order_no'                        => $order_no,
            'receiver_num'                    => count($rece),
            'receiver_list'                   => json_encode($receivers_arr, JSON_UNESCAPED_UNICODE),
            'order_price'                     => $orderinfo->total_pay_price,
            'order_profitsharing_after_price' => bcsub($orderinfo->total_pay_price, $profitsharing_price['total_money'], 2),
            'profitsharing_total_price'       => $profitsharing_price['total_money'],
            'profitsharing_status'            => 'wait',
            'business_name'                   => $orderinfo->sign, // 业务名
        ];

        ProfitsharingOrder::addOrder($insdata);

        // 设置子商户号
        $app = app('wechat.isvpay')->setSubMerchant($orderinfo->hotel_id);
        info([$orderinfo->trade_no, $receivers_arr]);

        info('分账订单号2:', [$profitsharing_no]);
        // 开始分账
        $sharing = $app->profit_sharing->share($orderinfo->trade_no, $profitsharing_no, $receivers_arr);
        addlogs('pc_profitsharing', [$orderinfo->trade_no, $profitsharing_no, $receivers_arr], $sharing, 0);
        // 正常返回处理中
        if (!empty($sharing['status']) && $sharing['status'] == 'PROCESSING') {
            $updata = [
                'transaction_id'       => $sharing['transaction_id'],
                'profitsharing_status' => $sharing['status'],
            ];
            ProfitsharingOrder::where(['profitsharing_no' => $profitsharing_no])->update($updata);

        } else {
            $updata = [
                'profitsharing_status' => 'fail',
            ];
            ProfitsharingOrder::where(['profitsharing_no' => $profitsharing_no])->update($updata);
            $error_msg = '-';
            // {"return_code":"FAIL","return_msg":"分账接收方列表格式错误"}
            if (!empty($sharing['return_msg'])) {
                $error_msg = $sharing['return_msg'];
            }
            //{"return_code":"SUCCESS","result_code":"FAIL","err_code":"NOT_SHARE_ORDER","err_code_des":"非分账订单不支持分账","mch_id":"1566291601","sub_mch_id":"1644702947","appid":"wxb66f6b5fdaa4ab7e","nonce_str":"10a9f26cd5134368","sign":"732CCF3C70E0C26F1097E70C6A426A81D386BC14495C8C9A51AAB9346283BF87"}
            if (!empty($sharing['err_code_des'])) {
                $error_msg = $sharing['err_code_des'];
            }
            return '请求分账失败:' . $error_msg;
        }
        return true;
    }

    // 分账查询
    public function profitsharingQuery($profitsharing_no) {
        $ProfitsharingOrder_info = ProfitsharingOrder::where(['profitsharing_no' => $profitsharing_no])->first();

        $app     = app('wechat.isvpay')->setSubMerchant($ProfitsharingOrder_info->hotel_id);
        $sharing = $app->profit_sharing->query($ProfitsharingOrder_info->transaction_id, $profitsharing_no);
        if (!empty($sharing['status']) && $sharing['status'] == 'FINISHED') {
            // 更新分账订单
            $updata = [
                'transaction_id'       => $sharing['transaction_id'],
                'profitsharing_status' => $sharing['status'],
            ];
            ProfitsharingOrder::where(['profitsharing_no' => $profitsharing_no])->update($updata);

            if (!is_array($sharing['receivers'])) {
                $receivers = json_decode($sharing['receivers'], true);
            } else {
                $receivers = $sharing['receivers'];
            }
            $wx_profitsharing_after_price = 0; // 解冻给分账方的金额
            // 产生分账子订单
            foreach ($receivers as $key => $items) {
                if (!empty($items['description']) && $items['description'] == '解冻给分账方') {
                    $wx_profitsharing_after_price = ($items['amount'] * 0.01);
                    continue;
                }
                $receiver_info = ProfitsharingReceiver::where(['hotel_id' => $ProfitsharingOrder_info->hotel_id, 'type' => $items['type'], 'account' => $items['account'],])->first();
                $insdata       = [
                    'hotel_id'             => $ProfitsharingOrder_info->hotel_id,
                    'profitsharing_no'     => $profitsharing_no,
                    'order_no'             => $ProfitsharingOrder_info->order_no,
                    "type"                 => $items['type'],
                    'rate'                 => $receiver_info->rate,
                    "receiver_id"          => $receiver_info->id,
                    'receiver_uid'         => $receiver_info->receiver_uid,
                    "profitsharing_price"  => ($items['amount'] * 0.01),
                    "description"          => $items['description'],
                    "profitsharing_status" => $items['result'],
                    "finish_time"          => $items['finish_time'],
                    "detail_id"            => $items['detail_id'],
                ];
                ProfitsharingOrderReceiver::addOrderReceiver($insdata);
            }

            // 更新业务订单 分账后还剩多少金额
            $profitsharing_after_price = $ProfitsharingOrder_info->profitsharing_after_price;

            Order::where(['order_no' => $ProfitsharingOrder_info->order_no])->update(['profitsharing_after_price' => $profitsharing_after_price]);


        }

        return $sharing;
    }


    // 给用户发送通知 公众号 模板消息 成功
    public function userGzhMsgtplBookingSuccess($orderinfo) {
        $openPlatform   = app('wechat.open');
        $wxgzh          = $openPlatform->hotelWxgzh($orderinfo->hotel_id);
        $oauthinfo      = $openPlatform->getOauthInfo('', $orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_gzh_msg_tpl_success'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_gzh_msg_tpl_success']) && !empty($orderinfo->user->gzh_openid)) {
            $tpl_data = [
                'touser'      => $orderinfo->user->gzh_openid,
                'template_id' => $hotel_settings['booking_gzh_msg_tpl_success'],
                'url'         => '',
                'miniprogram' => [
                    'appid'    => $oauthinfo->AuthorizerAppid,
                    'pagepath' => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                ],
                'data'        => [
                    'thing1'  => $orderinfo->hotel->name, // 酒店名称
                    'thing5'  => $orderinfo->room_type, // 房型名称
                    'amount2' => $orderinfo->total_cost, // 金额
                    'time3'   => $orderinfo->arrival_time . '~' . $orderinfo->departure_time, // 入离时间
                    'thing4'  => $orderinfo->booking_name, // 入住人
                ]
            ];
            $result   = $wxgzh->template_message->send($tpl_data);
            addlogs('userGzhMsgtplBookingSuccess', $tpl_data, $result);
        } else {
            // 未能发送
        }

        return true;
    }

    // // 给用户发送通知 公众号 模板消息 取消
    public function userGzhMsgtplBookingCancel($orderinfo) {
        $openPlatform   = app('wechat.open');
        $wxgzh          = $openPlatform->hotelWxgzh($orderinfo->hotel_id);
        $oauthinfo      = $openPlatform->getOauthInfo('', $orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_gzh_msg_tpl_cancel'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_gzh_msg_tpl_cancel']) && !empty($orderinfo->user->gzh_openid)) {
            $tpl_data = [
                'touser'      => $orderinfo->user->gzh_openid,
                'template_id' => $hotel_settings['booking_gzh_msg_tpl_cancel'],
                'url'         => '',
                'miniprogram' => [
                    'appid'    => $oauthinfo->AuthorizerAppid,
                    'pagepath' => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                ],
                'data'        => [
                    'thing1'            => $orderinfo->hotel->name, // 酒店名称
                    'character_string2' => $orderinfo->order_no, // 订单号
                    'thing4'            => $orderinfo->room_type, // 房型名称
                    'time3'             => $orderinfo->arrival_time . '~' . $orderinfo->departure_time, // 入离时间
                    'amount7'           => $orderinfo->total_cost, // 金额
                ]
            ];
            $result   = $wxgzh->template_message->send($tpl_data);
            addlogs('userGzhMsgtplBookingCancel', $tpl_data, $result);
        } else {
            // 未能发送
        }
        return true;
    }

    // // 给用户发送通知 公众号 模板消息 失败
    public function userGzhMsgtplBookingFail($orderinfo) {
        $openPlatform   = app('wechat.open');
        $wxgzh          = $openPlatform->hotelWxgzh($orderinfo->hotel_id);
        $oauthinfo      = $openPlatform->getOauthInfo('', $orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_gzh_msg_tpl_fail'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_gzh_msg_tpl_fail']) && !empty($orderinfo->user->gzh_openid)) {
            $tpl_data = [
                'touser'      => $orderinfo->user->gzh_openid,
                'template_id' => $hotel_settings['booking_gzh_msg_tpl_fail'],
                'url'         => '',
                'miniprogram' => [
                    'appid'    => $oauthinfo->AuthorizerAppid,
                    'pagepath' => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                ],
                'data'        => [
                    'thing1'            => $orderinfo->hotel->name, // 酒店名称
                    'thing2'            => $orderinfo->room_type, // 房型名称
                    'character_string6' => $orderinfo->order_no, // 订单号
                    'amount4'           => $orderinfo->total_cost, // 金额
                    'const5'            => '已满房', // 失败原因
                ]
            ];
            $result   = $wxgzh->template_message->send($tpl_data);
            addlogs('userGzhMsgtplBookingFail', $tpl_data, $result);
        } else {
            // 未能发送
        }
        return true;
    }

    // 给用户发送通知 小程序 订单确认
    public function userMinappMsgtplBookingSuccess($orderinfo) {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_success'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_minapp_msg_tpl_success']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_success'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                'data'        => [
                    'thing1'  => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'thing6'  => [
                        'value' => $orderinfo->room_type, // 入住房型
                    ],
                    'date2'   => [
                        'value' => $orderinfo->arrival_time, // 入住时间
                    ],
                    'date3'   => [
                        'value' => $orderinfo->departure_time, // 离店时间
                    ],
                    'amount4' => [
                        'value' => $orderinfo->total_cost, // 订单金额
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingSuccess', $tpl_data, $result);
        } else {
            // 未能发送
        }
        return true;
    }

    // 给用户发送通知 小程序 取消
    public function userMinappMsgtplBookingCancel($orderinfo, $reason = '行程改变') {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_cancel'], $orderinfo->hotel_id);

        if (!empty($hotel_settings['booking_minapp_msg_tpl_cancel']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_cancel'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                'data'        => [
                    'thing1'            => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'character_string8' => [
                        'value' => $orderinfo->order_no, // 订单号
                    ],
                    'amount4'           => [
                        'value' => $orderinfo->total_cost, // 订单金额
                    ],
                    'thing6'            => [
                        'value' => $orderinfo->room_type, // 房型名称
                    ],
                    'thing7'            => [
                        'value' => $reason, // 取消原因
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingSuccess', $tpl_data, $result);
        } else {
            // 未能发送
        }
        return returnData(200, 1, $result, 'ok');
    }

    // 给用户发送通知 小程序 失败
    public function userMinappMsgtplBookingFail($orderinfo, $reason = '已满房') {

        $openPlatform   = app('wechat.open');
        $miniProgram    = $openPlatform->hotelMiniProgram($orderinfo->hotel_id);
        $hotel_settings = \App\Models\Hotel\HotelSetting::getlists(['booking_minapp_msg_tpl_fail'], $orderinfo->hotel_id);
        $result         = [];
        if (!empty($hotel_settings['booking_minapp_msg_tpl_fail']) && !empty($orderinfo->user->openid)) {
            $tpl_data = [
                'template_id' => $hotel_settings['booking_minapp_msg_tpl_fail'], // 所需下发的订阅模板id
                'touser'      => $orderinfo->user->openid,     // 接收者（用户）的 openid
                'page'        => '/pages/order/detail?out_trade_no=' . $orderinfo->order_no,
                'data'        => [
                    'thing1'            => [
                        'value' => $orderinfo->hotel->name, // 酒店名称
                    ],
                    'character_string6' => [
                        'value' => $orderinfo->order_no, // 订单号
                    ],
                    'amount4'           => [
                        'value' => $orderinfo->total_cost, // 租金总额
                    ],
                    'thing5'            => [
                        'value' => $reason, // 失败原因
                    ],
                ],
            ];
            $result   = $miniProgram->subscribe_message->send($tpl_data);
            addlogs('userMinappMsgtplBookingFail', $tpl_data, $result);
        } else {
            // 未能发送
        }
        return returnData(200, 1, $result, 'ok');
    }

    // 全局订单退款
    public function fullOrderRefund($hotel_id, $order_no, $refund_reason) {
        $order          = Order::where(['order_no' => $order_no])->first();
        $refund_info    = OrderRefund::where(['order_no' => $order_no])->first();
        $trade_no       = $order->trade_no;
        $out_request_no = $refund_info->out_request_no;
        $isvpay         = app('wechat.isvpay');
        $app            = $isvpay->setSubMerchant($hotel_id);

        $refundFee = bcmul($order->total_pay_price, 100, 0);
        $result    = $app->refund->byTransactionId($trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => $refund_reason,
        ]);
        addlogs('refund_byTransactionId', [$trade_no, $out_request_no, $refundFee, $refundFee], $result);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {
            // 更新退款状态
            $update = [
                'status'      => OrderRefund::Status2,
                'refund_time' => date('Y-m-d H:i:s'),
            ];
            OrderRefund::where(['order_no' => $order->order_no])->update($update);

            // 更新业务订单状态
            if ($order->sign == 'tuangou') {
                TuangouOrderRelation::upOrderStatus($order->id, TuangouOrderRelation::Order_status_5);
            }
            return true;
        }
        $emsg = !empty($result['return_msg']) ? $result['return_msg'] : '-';
        return $emsg;
    }

}