<?php

namespace App\Merchant\Controllers;

use App\Merchant\Metrics\OrderTotal;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Box;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Admin;
use App\Models\Hotel\Setting;
use App\Models\Hotel\BookingOrder;
use App\Merchant\Metrics\Examples;
use Request;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        /*$card1 = Card::make('今日待办','');
        $card2 = Card::make('数据统计','');
        $card3 = Card::make('',$tab);*/
        $debug = Request::get('debug');
    
        return $content
            ->header('概况')
            ->description('')
            ->row(function (Row $row) {
                $row->column(9, function (Column $column) {
                    $qiantian = date('Ymd', strtotime('-2 days'));
                    $hotel_id = Admin::user()->hotel_id;
                    /*$miniProgram = app('wechat.open')->hotelMiniProgram($hotel_id);
                    if ($miniProgram !== false) {
                        $res = $miniProgram->data_cube->summaryTrend($qiantian, $qiantian);
                        addlogs('miniProgram_data_cube_summaryTrend', [''], $res, $hotel_id);
                    }

                    if (!empty($res['list'][0])) {
                        $miniapp_data_cube = $res['list'][0];
                    }*/

                    $miniapp_data_cube = [
                        'visit_total' => 0,
                        'share_pv' => 0,
                        'share_uv' => 0,
                        'ref_date' => 0,
                    ];
                    // 一列多行
                    $tab = new Tab();
                    $tab->add('今日新订', '');
                    $tab->add('当前在住', '');
                    $tab->add('今日预住', '');
                    $tab->add('今日预离', '');
                    $card11 = Card::make('', $this->daysWait())->withHeaderBorder();
                    //
                    $card13 = Card::make('数据统计', new Examples\NewUsers())->withHeaderBorder();
                    $column->row($card11);
                    if (!empty($miniapp_data_cube)) {

                        $row = new Row();
                        $row->column(4, function (Column $column) use ($miniapp_data_cube) {
                            $box1 = Box::make('累计用户数', '<div>' . $miniapp_data_cube['visit_total'] . '</div>');
                            $column->row($box1);
                        });
                        $row->column(4, function (Column $column) use ($miniapp_data_cube) {
                            $box2 = Box::make('转发次数', '<div>' . $miniapp_data_cube['share_pv'] . '</div>');
                            $column->row($box2);
                        });
                        $row->column(4, function (Column $column) use ($miniapp_data_cube) {
                            $box3 = Box::make('转发人数', '<div>' . $miniapp_data_cube['share_uv'] . '</div>');
                            $column->row($box3);
                        });
                        $card12 = Card::make('小程序数据分析', $row)->withHeaderBorder();
                        $card12->tool("<div>" . $miniapp_data_cube['ref_date'] . "</div>");
                        $column->row($card12);
                    }

                    $column->row($card13);
                    //$column->row(Card::make('',$tab));

                });

                $row->column(3, function (Column $column) {
                    $saasinfo = Admin::user()->getSaaSVersionInfo();
                    $htmls = <<<HTML
        <ul class="nav nav-pills nav-sidebar flex-column">
        <li class="nav-item" style="line-height: 30px">系统版本: <span class="text-danger">{$saasinfo['saas_version_name']}</span> </li>
        <li class="nav-item" style="line-height: 30px">到期时间: <span class="text-success">{$saasinfo['saas_version_expired_at']}</span></li>
</ul>
HTML;

                    $card0 = Card::make('系统信息', $htmls)->withHeaderBorder();
                    $column->row($card0);
                    $card1 = Card::make('服务热线', $this->serviceHotline())->withHeaderBorder();
                    //$card2 = Card::make('帮助中心','')->withHeaderBorder();
                    $card3 = Card::make('公告', $this->gonggao())->withHeaderBorder();
                    $card3->tool('');
                    $column->row($card1);
                    //$column->row($this->help());
                    //$column->row("<br/>");
                    //$column->row($card3);
                    // $card
                });
            });

        /*->row($card1)
            ->row($card2)
            ->row($card3);*/
        /*->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(3, new OrderTotal());
                        $row->column(3, new OrderTotal());
                        $row->column(3, new OrderTotal());
                        $row->column(3, new OrderTotal());
                    });

                    //$column->row(new Examples\Tickets());
                });

                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(6, new Examples\NewUsers());
                        $row->column(6, new Examples\NewDevices());
                    });

                    $column->row(new Examples\Sessions());
                    $column->row(new Examples\ProductOrders());
                });
            });*/
    }

    public function daysWait()
    {
        $count_data = [
            'days_order_num' => 0,
            'days_sale_price' => 0,
            'yesterday_sale_price' => 0,
            'days7_sale_price' => 0,
        ];

        //if (Request::get('debug') == '1') {
            $hotel_id = Admin::user()->hotel_id;
            $where1 = [
                ['hotel_id', '=', $hotel_id],
                ['status', '>', 2],
                ['created_at', '>=', date('Y-m-d 00:00:00')]
            ];
            $days_order_num = BookingOrder::where($where1)->count();

            $where2 = [
                ['hotel_id', '=', $hotel_id],
                ['status', '>', 2],
                ['created_at', '>=', date('Y-m-d 00:00:00')]
            ];
            $days_sale_price = BookingOrder::where($where2)->sum('total_cost');

            $where3 = [
                ['hotel_id', '=', $hotel_id],
                ['status', '>', 2],
                ['created_at', '>=', date('Y-m-d 00:00:00', strtotime('-1 day'))],
                ['created_at', '<', date('Y-m-d 00:00:00')]
            ];

            $yesterday_sale_price = BookingOrder::where($where3)->sum('total_cost');
            $where4 = [
                ['hotel_id', '=', $hotel_id],
                ['status', '>', 2],
                ['created_at', '>=', date('Y-m-d 00:00:00', strtotime('-7 day'))],
                ['created_at', '<', date('Y-m-d 00:00:00')]
            ];
            $days7_sale_price = BookingOrder::where($where4)->sum('total_cost');

            $count_data = [
                'days_order_num' => $days_order_num,
                'days_sale_price' => $days_sale_price,
                'yesterday_sale_price' => $yesterday_sale_price,
                'days7_sale_price' => $days7_sale_price,
            ];
            
        //}

        $htmls = <<<HTML
<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info">
            <i class="fa fa-envelope"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">今日订单总数</span>
                <span class="info-box-number">{$count_data['days_order_num']}</span>
            </div>

        </div>

    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fa fa-flag"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">今日销售额</span>
                <span class="info-box-number">{$count_data['days_sale_price']}</span>
            </div>

        </div>

    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-black"><i class="feather icon-award"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">昨日销售额</span>
                <span class="info-box-number">{$count_data['yesterday_sale_price']}</span>
            </div>

        </div>

    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fa fa-star"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">近7天销售额</span>
                <span class="info-box-number">{$count_data['days7_sale_price']}</span>
            </div>

        </div>

    </div>

</div>
HTML;

        return $htmls;
    }
    // 公告
    public function gonggao()
    {
        $tab = Tab::make();
        $tab->addLink('v1.0.0版本即将上线', '#');

        /*$tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');
        $tab->addLink('v1.0.01版本即将上线','');*/

        return $tab->vertical();
    }
    //
    public function serviceHotline()
    {
        $formdata7 = Setting::getlists([], 'platform_fuwu_info');
        $wxqrcode = asset('images/wxqrcode.png');
        $fuwuphone = '17681849188';
        if (!empty($formdata7['fuwu_wx'])) {
            $wxqrcode = $formdata7['fuwu_wx'];
        }
        if (!empty($formdata7['fuwu_phone'])) {
            $fuwuphone = $formdata7['fuwu_phone'];
        }
        $htmls = <<<HTML
        <ul class="nav nav-pills nav-sidebar flex-column">
        <li class="nav-item"> <i class="feather icon-phone"></i> $fuwuphone</li>
        <li class="nav-item" style="text-align: center"> <img class="" src="$wxqrcode" width="200"></li>
</ul>
HTML;


        return $htmls;
    }

    // 帮助中心
    public function help()
    {
        $bgimg = asset('images/help.jpeg');
        $html = <<<HTML
        <div style="min-height:80px;color:#ffffff;font-size:22px;font-weight:bold;text-align: center;padding-top: 25px;background-image: url($bgimg);background-repeat:no-repeat;background-size:100% 100%">帮助中心</div>
HTML;
        return $html;
    }

    /**
     *
     */
    public function box1()
    {
        $html = <<<HTML
<div class="small-box bg-info">
    <div class="inner">
        <h3>150</h3>
        <p>New Orders</p>
    </div>
    <div class="icon">
        <i class="fas fa-shopping-cart"></i>
    </div>
    <a href="#" class="small-box-footer">
        More info <i class="fas fa-arrow-circle-right"></i>
    </a>
</div>
HTML;
        return $html;
    }
}
