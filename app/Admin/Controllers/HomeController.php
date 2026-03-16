<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Admin as adMet;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Linkbox;
use Dcat\Admin\Widgets\MediaList;
use Dcat\Admin\Widgets\Callout;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\ListGroup;
use Dcat\Admin\Widgets\InfoList;
use Dcat\Admin\Widgets\CoverCard;
use PDO;
class HomeController extends Controller
{
    public function index(Content $content)
    {
        $user = Admin::user();
        if(Admin::user()->isRole('run2')){
            return $this->run2($content);
        }
        return $content
            ->header('酒旅智慧管理系统')
            ->description('高效管理 降本增效')
            ->row(function (Row $row) {
                /*$row->column(12, function (Column $column) {
                    $column->row(Dashboard::title());
                    $column->row(new Examples\Tickets());
                });*/

                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(2, new adMet\HotelTotal('酒店总数'));
                        $row->column(2, new adMet\HotelTotal('预订单量'));
                        $row->column(2, new adMet\HotelTotal('预订交易额'));
                        $row->column(2, new adMet\HotelTotal('总会员数'));
                        $row->column(2, new adMet\HotelTotal('扫码购订单'));
                        $row->column(2, new adMet\HotelTotal('扫码购总交易'));
                    });

                    /*$column->row(new Examples\Sessions());
                    $column->row(new Examples\ProductOrders());*/
                });
                

                $row->column(12, function (Column $column) {
                    
                });
            })
            ->row(function (Row $row) {
                    $row->column(3, function (Column $column) {
                        // $cover_card = CoverCard::make()->add('开源公众号','关注公众号 随时了解更新动态')
                        //     ->bg('https://dcat-plus.saishiyun.net/img/card-bg1.jpeg')
                        //     ->avatar('https://dcat-plus.saishiyun.net/img/wxgzh_qrcode.jpg');
                        // $column->row($cover_card->render());
                        // $cover_card1 = CoverCard::make()->add('赞助捐助开源','鼓励作者持续更新')
                        //     ->bg('https://dcat-plus.saishiyun.net/img/card-bg2.jpeg')
                        //     ->avatar('https://dcat-plus.saishiyun.net/img/weixinpay.jpg');
                        // $column->row($cover_card1->render());
                        $group = ListGroup::make();

                        // 获取已安装扩展包信息
                        $installedPackages = json_decode(file_get_contents(base_path('vendor/composer/installed.json')), true);

                        // 指定要获取版本号的扩展包名称
                        $packageName = 'dcat-plus/laravel-admin';

                        // 查找指定扩展包的版本号
                        $packageVersion = '--';
                        if(!empty($installedPackages['packages'])){
                            foreach ($installedPackages['packages'] as $package) {
                                if ($package['name'] === $packageName) {
                                    $packageVersion = $package['version'];
                                    break;
                                }
                            }
                        }
                        $group->add('Dcat-plus Admin  Version',  $packageVersion,'#');
                        $group->add('PHP Version',  phpversion(),'#');
                        $group->add('Laravel Version', app()->version(),'#');
                        $group->add('Mysql Version', app('db')->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),'#');
                        $column->row($group->render());

                    });
                });
    }

    public function run2($content){
        return $content
            ->header('酒旅智慧管理系统')
            ->description('高效管理 降本增效')
            ->body('');
    }
}
