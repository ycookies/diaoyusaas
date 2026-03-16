<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\BookingOrderCancel;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\WxappConfig;
use App\User;
use EasyWeChat\Factory;
use App\Models\Hotel\Refund;
use App\Models\Hotel\OrderRefund;
// 订单取消事件 处理
class OrderCancel
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BookingOrderCancel $event)
    {
        $hotel_id = $event->order->hotel_id;
        info('取消订单，资金退回中..');
        $where = [];
        $where[] = ['hotel_id','=',$hotel_id];
        $where[] = ['out_trade_no','=',$event->order->out_trade_no];
        $detail = BookingOrder::where($where)->first();

        // 创建退款订单
        $out_request_no = 'R' . time();
        $refund_order = [
            'hotel_id' => $detail->hotel_id,
            'uid'        => $detail->user_id,
            'refund_no'  => $out_request_no,
            'order_no'   => $detail->out_trade_no,
            'mode'       => 'wx',
            'status'     => '1',
            'total_fee'  => $detail->total_cost,
            'refund_fee' => $detail->total_cost,
        ];
        $res_status   = Refund::firstOrCreate(['order_no' => $detail->out_trade_no], $refund_order);

        if(!$res_status){
            return '创建退款订单失败';
        }

        $res_status = Refund::where(['refund_no'=> $out_request_no])->first();

        OrderRefund::createOrder($res_status,'客房已满','hotel_room_booking','商家');

        /*$config = WxappConfig::getConfig($hotel_id);
        $app    = Factory::payment($config);
        $app->setSubMerchant($config['sub_mch_id']);*/

        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $app    = $isvpay->setSubMerchant($hotel_id);

        $refundFee = bcmul($detail->total_cost, 100, 0);
        // 参数分别为：微信订单号、商户退款单号、订单金额、退款金额、其他参数
        //$app->refund->byTransactionId(string $transactionId, string $refundNumber, int $totalFee, int $refundFee, array $config = []);

        $result = $app->refund->byTransactionId($detail->trade_no, $out_request_no, $refundFee, $refundFee, [
            'refund_desc' => '客房已满',
        ]);
        info('退订',[$refund_order,$result,$detail->trade_no, $out_request_no, $refundFee, $refundFee]);
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && !empty($result['refund_id'])) {
            BookingOrder::where($where)->update(['status' => BookingOrder::Status7]);
            OrderRefund::where(['order_no' => $detail->out_trade_no])->update(['status' => OrderRefund::Status2, 'refund_time' => date('Y-m-d H:i:s')]);
        }
    }
}
