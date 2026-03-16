<?php

namespace App\Api\Portal;

use App\Admin;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use App\Models\Hotel\BookingOrder;

// 微信小程序
class NotifyController extends AController {

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
    // 微信小程序支付通知
    public function wxPayNotify(Request $request) {
        info('小程序支付通知-总通知');
        info($request->input());
        die('ok');

    }

    // 旅忆行 微信小程序支付通知
    public function wxPayNotifyWx8c3d9b0bbf9272bc(Request $request) {
        // 创建一个 DateTime 对象
        $config   = config('wechat.min1');
        $app      = Factory::payment($config);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 你的逻辑
            if($message['result_code'] == 'SUCCESS'){
                $out_trade_no = $message['out_trade_no'];
                // 订房订单
                if(strpos($out_trade_no,'YXB') !== false){
                    $date = \DateTime::createFromFormat('YmdHis', $message['time_end']);
                    $formattedDate = date_format($date, 'Y-m-d H:i:s');
                    $orderinfo = BookingOrder::where(['out_trade_no'=>$out_trade_no])->first();
                    if($orderinfo->status != 2){
                        info('更新订单');
                        $updata = [
                            'status' => 2,
                            'trade_no' => $message['transaction_id'],
                            'pay_time' => $formattedDate,
                        ];
                        BookingOrder::where(['out_trade_no'=>$out_trade_no])->update($updata);
                    }
                    // 驱动订单接单提醒
                }

            }
            info('小程序支付通知');
            info($message);
            return true; // 返回处理完成
        });
        return $response->send();
    }
}
