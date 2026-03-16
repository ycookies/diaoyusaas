<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Merchant\Controllers\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Services\NuonuoService;
use Illuminate\Http\Request;
use App\Mail\InvoiceTonuonuoKaihu;
use Illuminate\Support\Facades\Mail;
use App\Models\Hotel\InvoiceRegister;
use Dcat\Admin\Widgets\Form as WidgetForm;

// 电子发票
class InvoiceController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('电子发票 ')
            ->description('消费开票高效便捷')
            ->breadcrumb(['text'=>'电子发票','uri'=>''])
            ->body($this->pageMain());
            //->body('<h2 style="color:#a3a3a3;margin-top: 150px;text-align: center">近期上线...</h2>');
    }

    // 页面
    public function pageMain() {
        $data = [];
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('电票商户授权', $this->tab1());
        //$tab->add('微信小程序支付信息配置', $this->tab2());
        //$tab->add('微信小程序 模板消息', $this->tab3());
        //$tab->add('轮播图', $this->tab3());
        //$tab->add('导航图标', $this->tab4());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    public function tab1(){
        $service = new NuonuoService();
        $url   = $service->getOauthUrl();
        $htmls = '<a target="_blank" href="' . $url . '"> >>> 去授权</a>  ';
        $htmls .= '<button type="button" class="btn btn-primary text-capitalize" onclick="window.location.reload()"><i class="feather icon-refresh-ccw"></i> 刷新 </button>';
        return $card = Card::make('扫码授权', $htmls);
    }

    // 邮件通知nuonuo
    public function emailToNuonuo(Request $request){
        $hotel_id = Admin::user()->hotel_id;
        $info = InvoiceRegister::where(['hotel_id'=> $hotel_id])->first();
        $mail1 = 'liuzhenyu@szhtxx.com';
        $mail2 = 'fengshangshi@szhtxx.com';
        $mmk       = Mail::to('124355733@qq.com')
            //->cc('fengshangshi@szhtxx.com')
            //->cc('liuzhenyu@szhtxx.com')
            ->send(new InvoiceTonuonuoKaihu($info));
        if(empty($mmk)){
            return (new WidgetForm())->response()->success('发送成功')->refresh();
        }
        return (new WidgetForm())->response()->error('发送失败');
    }



}
