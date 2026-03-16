<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\MinpayAsynNotify;
use App\Services\RongbaoPayService;
use Dcat\Admin\Http\JsonResponse;
use Illuminate\Http\Request;

// 综合处理器

class OverallController extends Controller {

    // 重发交易数据给融宝支付系统
    public function resetAsynNotify(Request $request) {
        $id   = $request->get('id');
        $info = MinpayAsynNotify::find($id);
        if (!$info) {
            return JsonResponse::make()->error('未找到日志信息');
        }
        // 检查是否已经发送成功过
        if (!empty($info->status)) {
            return JsonResponse::make()->error('已是转发成功状态,无须重发'.$info->status);
        }
        $notice_data = json_decode($info->send_data, true);
        $service     = new RongbaoPayService();
        $res         = $service->sendapi('api/payment_min_pay/wxNotifyUrlPayCode', $notice_data);
        $res_arr     = $res;
        $res_str     = $res;
        if (!is_array($res_arr)) {
            $res_arr = json_decode($res, true);
        }
        if (is_array($res)) {
            $res_str = json_encode($res_str, JSON_UNESCAPED_UNICODE);
        }
        $out_trade_no = $notice_data['out_trade_no'];
        $orderinfo    = BookingOrder::with('room', 'hotel')->where(['out_trade_no' => $out_trade_no])->first();
        $booking_msg = '预订酒店:' . $orderinfo->hotel->name . ',预订人:' . $orderinfo->booking_name . '，联系电话:' . $orderinfo->booking_phone;
        $insdata['resp_data'] = $res_str;

        if (!empty($res_arr['msg']) && $res_arr['msg'] == '订单号已存在') {
            $insdata['status'] = 1;
            MinpayAsynNotify::where(['id' => $id])->update($insdata);
            return JsonResponse::make()->error('已经转发成功,无须重发');
        }

        if (!empty($res_arr['code']) && $res_arr['code'] == 1) {
            // 把订房通知发到企业微信群
            WxRobotBooking('有人订房', $booking_msg, $out_trade_no);
            $insdata['status'] = 1;
            MinpayAsynNotify::where(['id' => $id])->update($insdata);
            return JsonResponse::make()->success('转发成功')->refresh();
        }

        // 转发失败
        WxRobotError('推送支付信息出错', '订单号：' . $out_trade_no);
        $insdata['status']    = 0;
        MinpayAsynNotify::where(['id' => $id])->update($insdata);

        return JsonResponse::make()->error('转发失败:' . $res_str);
    }
}
