<?php

namespace App\Services;

use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\Order\Order;
use App\Models\Hotel\Order\OrderDetail;
use App\Models\Hotel\OrderRefund;
use App\Models\Hotel\ProfitsharingOrder;
use App\Models\Hotel\ProfitsharingOrderReceiver;
use App\Models\Hotel\ProfitsharingReceiver;
use App\Models\Hotel\Refund;


/**
 * 微信支付订单分账服务
 * @package App\Services
 * anthor Fox
 */
class ProfitsharingService extends BaseService {

    // 订房订单开始分账
    public function profitsharingToBooking($out_trade_no) {
        $where[]   = ['out_trade_no', '=', $out_trade_no];
        $orderinfo = BookingOrder::where($where)->first();

        if (empty($orderinfo->is_profitsharing)) {
            return '未开启订房交易分账';
        }

        // 查询是否已经分账过
        $res_count = ProfitsharingOrder::where(['order_no'=> $out_trade_no])->count();
        if($res_count > 0 ){
            return '已经生成了分账,不能再次操作';
        }


        // 查看是否有分账接受方
        $rece = ProfitsharingReceiver::where(['hotel_id' => $orderinfo->hotel_id])->get();
        if (!$rece) {
            return '此酒店没有分账接受方';
        }
        $profitsharing_no = "PF" . $orderinfo->hotel_id . time();

        // 计算参于分账人数和分账支出总金额
        $profitsharing_price = ProfitsharingReceiver::jisuanProfitsharingOutMoney($orderinfo->total_cost, $rece);
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
            'order_no'                        => $out_trade_no,
            'receiver_num'                    => count($rece),
            'receiver_list'                   => json_encode($receivers_arr, JSON_UNESCAPED_UNICODE),
            'order_price'                     => $orderinfo->total_cost,
            'order_profitsharing_after_price' => bcsub($orderinfo->total_cost, $profitsharing_price['total_money'], 2),
            'profitsharing_total_price'       => $profitsharing_price['total_money'],
            'profitsharing_status'            => 'wait',
            'business_name'                   => 'hotel_room_booking',
        ];

        ProfitsharingOrder::addOrder($insdata);

        // 设置子商户号
        $app = app('wechat.isvpay')->setSubMerchant($orderinfo->hotel_id);
        info([$orderinfo->trade_no, $receivers_arr]);
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

    // 其它订单开始分账
    public function profitsharing($order_no) {
        $where[]   = ['order_no', '=', $order_no];
        $orderinfo = Order::where($where)->first();

        if (empty($orderinfo->is_profitsharing)) {
            return '未开启订房交易分账';
        }
        // 查询是否已经分账过
        $res_count = ProfitsharingOrder::where(['order_no'=> $order_no])->count();
        if($res_count > 0 ){
            return '已经生成了分账,不能再次操作';
        }

        // 查看是否有分账接受方
        $rece = ProfitsharingReceiver::where(['hotel_id' => $orderinfo->hotel_id])->get();
        if (!$rece) {
            return '此酒店没有分账接受方';
        }
        $profitsharing_no = "PF" . $orderinfo->hotel_id . time();

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
            'business_name'                   => 'hotel_room_booking',
        ];

        ProfitsharingOrder::addOrder($insdata);

        // 设置子商户号
        $app = app('wechat.isvpay')->setSubMerchant($orderinfo->hotel_id);
        info([$orderinfo->trade_no, $receivers_arr]);
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

    /**
     * @desc  分账查询
     * @param $out_order_no 分账订单号
     * @return string
     * author eRic
     * dateTime 2025-03-12 11:47
     */
    public function profitsharingQuery($out_order_no) {
        $orderinfo = ProfitsharingOrder::where(['profitsharing_no' => $out_order_no])->first();
        if(!$orderinfo){
            return '未找到分账订单';
        }
        $profitsharing_no = $orderinfo->profitsharing_no;
        $app     = app('wechat.isvpay')->setSubMerchant($orderinfo->hotel_id);
        $sharing = $app->profit_sharing->query($orderinfo->transaction_id, $profitsharing_no);
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
                $receiver_info = ProfitsharingReceiver::where(['hotel_id' => $orderinfo->hotel_id, 'type' => $items['type'], 'account' => $items['account'],])->first();
                $insdata       = [
                    'hotel_id'             => $orderinfo->hotel_id,
                    'profitsharing_no'     => $profitsharing_no,
                    'order_no'             => $orderinfo->order_no,
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
            if (substr($orderinfo->order_no, 0, 2) == '11') {
                \App\Models\Hotel\BookingOrder::update_profitsharing_after_price($orderinfo->order_no,$orderinfo->order_profitsharing_after_price);
            }else{

                $status = \App\Models\Hotel\Order\Order::update_profitsharing_after_price($orderinfo->order_no,$orderinfo->order_profitsharing_after_price);
            }

            /*$profitsharing_after_price = $orderinfo->profitsharing_after_price;
            $orderObj->profitsharing_after_price = $profitsharing_after_price;
            $orderObj->save();*/

        }
        return $sharing;
    }

}