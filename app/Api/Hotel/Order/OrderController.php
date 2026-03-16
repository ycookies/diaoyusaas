<?php

namespace App\Api\Hotel\Order;

use App\Api\Hotel\BaseController;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Order\Order;
use App\Models\Hotel\OrderRefund;
use App\Models\Hotel\Tuangou\TuangouOrderRelation;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

// 全局订单
class OrderController extends BaseController {

    // 订单列表
    public function orderList(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $page     = $request->get('page');
        $pagesize = $request->get('pagesize', 20);
        $hotel_id = $request->get('hotel_id');
        $status   = $request->get('status');
        $where    = [
            'hotel_id' => $hotel_id,
        ];
        if (!empty($status)) {
            $where['status'] = $status;
        }
        $list = Order::where($where)
            ->orderBy('id', 'DESC')
            ->paginate($pagesize);

        return returnData(200, 1, $this->pageintes($list), 'ok');
    }


    // 订单详情
    public function orderDetail(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id = $request->get('hotel_id');
        $order_no = $request->get('order_no');
        $where    = [
            'hotel_id' => $hotel_id,
            'order_no' => $order_no
        ];

        $info = Order::with('goods', 'detail','refund', 'goods.warehouse')
            ->where($where)
            ->first();

        if (!$info) {
            return returnData(404, 0, [], '找不到订单信息');
        }

        return returnData(200, 1, ['info' => $info], 'ok');

    }

    // 团购订单退款
    public function applyOrderRefund(Request $request) {
        $user     = JWTAuth::parseToken()->authenticate();
        $hotel_id      = $request->get('hotel_id');
        $order_no      = $request->get('order_no');
        $refund_reason = $request->get('refund_reason');
        if (empty($refund_reason)) {
            return returnData(205, 0, [], '请填写退款原因');
        }
        $order = Order::where(['user_id'=> $user->id,'order_no' => $order_no])->first();
        if (empty($order->id)) {
            return returnData(205, 0, [], '非法操作.找不到订单信息');
        }
        if (empty($order->trade_no)) {
            return returnData(205, 0, [], '非法操作.订单未支付');
        }
        /*if(!empty($order->clerk_id)){
            return returnData(205, 0, [], '订单已核销,无法线上退款');
        }*/
        // 创建预订退款订单
        $out_request_no = 'RE' . $order->order_no;
        $refund_price   = $order->total_pay_price;
        $fee_price      = $refund_price;

        $refund_decs = $refund_reason;
        $mdata       = [
            'hotel_id'       => $order->hotel_id,
            'room_id'        => 0,
            'user_id'        => $order->user_id,
            'order_no'       => $order->order_no,
            'out_request_no' => $out_request_no,
            'sign'           => $order->sign,
            'cost'           => $refund_price,
            'fee_price'      => $fee_price,
            'refund_desc'    => $refund_decs,
            'status'         => OrderRefund::Status1,
        ];
        $res_status  = OrderRefund::firstOrCreate(['order_no' => $order->order_no], $mdata);

        // 获取业务退款配置信息
        $key_refund_sign = 'is_' . $order->sign . '_refund_verify';
        $flds            = [$key_refund_sign];

        // 是否退款审核
        $formdata = HotelSetting::getlists($flds, $hotel_id);
        if (!empty($formdata[$key_refund_sign])) {
            return returnData(200, 1, [], '退订申请已经提交');
        }
        // 无需审核，直接退款
        $service = new \App\Services\OrderService();
        $status = $service->fullOrderRefund($order->hotel_id,$order->order_no,$refund_decs);
        if($status === true){
            return returnData(200, 1, ['refund_id' => ''], '退款成功');
        }

        return returnData(205, 0, [], '退款失败：' . $status);
    }

}
