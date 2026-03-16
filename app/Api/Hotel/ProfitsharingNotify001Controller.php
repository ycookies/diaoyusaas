<?php

namespace App\Api\Hotel;

use App\Admin;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\HotelDevice;
use App\Models\Hotel\ProfitsharingOrder;
use App\Models\Hotel\Setting;
use App\Models\Hotel\WxappConfig;
use App\Services\RongbaoPayService;
use App\Services\BookingOrderService;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as AController;
use App\Models\Hotel\MemberOrder;

// 分账动账通知
class ProfitsharingNotifyController extends AController {

    public $im_relation;
    private $openid;
    private $msgcon;
    private $mall_id;
    public $config;

    // 分账动账通知
    public function notify(Request $request) {
        info('分账动账通知');
        $this->queryFullProfitsharing();
        info($request->all());
        return 'ok';
        //
        $app = app('wechat.isvpay')->make();
        //$this->config = WxappConfig::getToappidConfig($id);
        //$app          = Factory::payment($this->config);
        $response     = $app->handlePaidNotify(function ($message, $fail) {
            // 你的逻辑
            info($message);

        });
        return $response->send();
    }

    // 检查全部分账结果
    public function queryFullProfitsharing(){
        $service = new BookingOrderService();
        $lists = ProfitsharingOrder::where(['profitsharing_status'=>'PROCESSING'])->get();
        foreach ($lists as $key => $items) {
            $res = $service->profitsharingQuery($items->profitsharing_no);
        }
        return 'ok';
    }

}
