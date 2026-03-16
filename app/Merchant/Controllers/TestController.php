<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */

namespace App\Merchant\Controllers;

use App\Models\Hotel\Help;
use App\Models\Hotel\HelpType;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

// 用于测试
class TestController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {

       //$res =  $service->profitsharingQuery('212025031210481776');
        //$model = \App\Models\Hotel\TicketsCode::generateCode('TG2025021118130818');
        info('22222');
        $optional = [
            'page'=> 'pages/index/index',
        ];
        $hotel_id = 228;
        //$minapp_qrcode = app('wechat.open')->getUnlimitedQRCode('',$hotel_id,'',$optional,$hotel_id.'-qrcode.png',1);
        echo "<pre>";
        var_dump($hotel_id);
        echo "</pre>";
        exit;
        echo "<pre>";
        print_r([123333]);
        echo "</pre>";
        exit;
        /*$message['success_time'] = '2025-02-10T22:41:08+08:00';
        $date                = \DateTime::createFromFormat('YmdHis', $message['success_time']);
        $formattedDate       = date_format($date, 'Y-m-d H:i:s');*/

        $dateTime = new \DateTime('2025-02-10T22:41:08+08:00');
        $dateTime->setTimezone(new \DateTimeZone('Asia/Shanghai')); // 设置为东八区
        $formattedDate =  $dateTime->format('Y-m-d H:i:s');
        echo "<pre>";
        print_r($formattedDate);
        echo "</pre>";
        exit;

        //$model = \App\Models\Hotel\TicketsCode::generateCode('TG2025021118130818');
        //$model = \App\Models\Hotel\TicketsCode::verify('TG2025012422300940','36631896','1','');
        $hotel_id = Admin::user()->hotel_id;
        $isvpay = app('wechat.isvpay');
        $config = $isvpay->getOauthInfo('', $hotel_id);
        $pay_instance = (new \App\libary\WeChatPay\WeChatPay())->makePay();
        $out_trade_no = 'TS'.time();
        $resp = $pay_instance
            ->chain('v3/pay/partner/transactions/jsapi')
            ->post(['json' => [
                'sp_appid' => '',
                'sp_mchid'=>'',
                'sub_mchid' => '',
                'description' => '',
                'out_trade_no' => $out_trade_no,
                'notify_url'=> '',
                'amount'       => [
                    'total'    => 1,
                    'currency' => 'CNY'
                ],
                'payer' => [
                    'sub_openid'=> '',
                ],

            ]]);

        echo "<pre>";
        print_r($resp->getBody());
        echo "</pre>";
        exit;

        return $content
            ->header('帮助中心')
            ->description('全部')
            ->breadcrumb(['text'=>'帮助中心','uri'=>''])
            ->body($this->grid());
    }

}
