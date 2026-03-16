<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Admin as adMet;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Admin;
use App\Models\Hotel\BookingOrder;
use App\Models\Hotel\MerchantUser;
use Illuminate\Support\Facades\URL;
use App\User;
use App\Models\MerchantUser as Muser;
// 测试专用
class DemoController extends Controller
{
    public function index(Content $content)
    {

        $uid = '200031';
        $url = URL::signedRoute('autologin', ['user' => Muser::where(['id'=>$uid])->first()]);

    }

// 分账测试
    public function ProfitsharingTest(){
        $order_no  = request()->get('order_no');
        if(empty($order_no)){
            die('订单号不能为空');
        }
        // $service = new \App\Services\BookingOrderService();
        // $res = $service->profitsharing($order_no);
        echo "<pre>";
        print_r(['123']);
        echo "</pre>";
        exit;
    }

}
