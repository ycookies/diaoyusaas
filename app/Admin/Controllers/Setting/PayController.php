<?php

namespace App\Admin\Controllers\Setting;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Illuminate\Routing\Controller;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Hotel\Setting;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Form\NestedForm;
// 全局配置 支付设置
class PayController extends Controller
{
    public function index(Content $content){
        return $content
            ->header('支付设置')
            ->description('')
            ->breadcrumb(['text'=> '支付设置','uri'=> ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain(){
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('开关',$this->tab1());
        $tab->add('微信支付服务商', $this->tab2());
        $tab->add('微信公众号', $this->tab3());
        $tab->add('微信小程序', $this->tab4());
        $tab->add('微信开放平台', $this->tab5());
        $tab->add('企业微信', $this->tab6());
        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    // 开关项
    public function tab1(){
        $form = Form::make(new Setting());
        $form->action('setting-edit');
        $form->radio('wxpay_open','微信支付')->options(['1'=>'开','0'=> '关'])->value('1')->help('影响整个支付交易,请谨慎操作');
        $form->disableResetButton();
        $form->disableSubmitButton();
        //$card =  Card::make('开关',$form);
        return $form;
    }

    // 微信支付服务商
    public function tab2(){
        $formdata = Setting::getlists([],'pay_isv');
        $form = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('pay_isv');
        $form->html('<h3>微信支付服务商</h3>');
        $form->text('isv_app_id','app_id')->required();
        $form->text('isv_secret','secret')->required();
        $form->text('isv_mch_id','服务商 商户号')->required();
        $form->text('isv_notify_url','异步通知地址');
        $form->disableResetButton();
        $card =  Card::make('支付服务商参数',$form);
        return $card;
    }

    // 微信公众号
    public function tab3(){
        $formdata = Setting::getlists([],'wx_gzh');
        $form = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wx_gzh');
        $form->html('<h3>微信公众号</h3>');
        $form->text('gzh_app_id','app_id')->required();
        $form->text('gzh_secret','secret')->required();
        $form->text('gzh_notify_url','异步通知地址')->placeholder('');
        $form->text('gzh_Token','Token')->placeholder('');
        $form->text('gzh_aesKey','aesKey')->placeholder('');
        $form->text('gzh_staff','staff')->placeholder('');
        $form->text('gzh_wxRobot','wxRobot')->placeholder('');
        $form->html('温馨提示：获取前请先确认您已获得模板消息的使用权限，并且模板消息中没有任何数据。获取后请不要到公众号后台 删除相应的模板消息，否则会影响模板消息正常使用。');
        $form->table('gzh_template', '模板消息', function (NestedForm $table) {
            $table->text('gzh_template_name', '模板名')->prepend('')->required();
            $table->text('gzh_template_id', '模板ID')->prepend('')->required();
        });
        $form->disableResetButton();
        $card =  Card::make('微信公众号参数配置',$form);
        return $card;
    }
    // 微信小程序
    public function tab4(){
        $formdata = Setting::getlists([],'wxmin');
        $form = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wxmin');
        $form->html('<h3>微信小程序</h3>');
        $form->text('wxmin_app_id','app_id')->required();
        $form->text('wxmin_secret','secret')->required();
        $form->text('wxmin_notify_url','异步通知地址')->placeholder('');
        $form->text('wxmin_Token','Token')->placeholder('');
        $form->text('wxmin_aesKey','aesKey')->placeholder('');
        $form->text('wxmin_staff','staff')->placeholder('');
        $form->text('wxmin_wxRobot','wxRobot')->placeholder('');
        $form->html('温馨提示：获取前请先确认您已获得模板消息的使用权限，并且模板消息中没有任何数据。获取后请不要到公众号后台 删除相应的模板消息，否则会影响模板消息正常使用。');
        $form->table('wxmin_template', '订阅消息', function (NestedForm $table) {
            $table->text('wxmin_template_name', '消息模板名')->prepend('')->required();
            $table->text('wxmin_template_id', '消息ID')->prepend('')->required();
        });
        $form->disableResetButton();
        $card =  Card::make('微信小程序参数配置',$form);
        return $card;
    }
    // 微信开放平台
    public function tab5(){
        $formdata = Setting::getlists([],'wxopen');
        $form = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wxopen');
        $form->html('<h3>微信开放平台</h3>');
        $form->text('wxopen_app_id','app_id')->required();
        $form->text('wxopen_secret','secret')->required();
        $form->text('wxopen_Token','Token')->required();
        $form->text('wxopen_aesKey','aesKey')->required();
        $form->text('wxopen_oauth_url','授权回调地址')->placeholder('异步通知地址');
        $form->text('wxopen_notify_url','消息通知地址')->placeholder('异步通知地址');

        $form->disableResetButton();
        $card =  Card::make('微信开放平台参数配置',$form);
        return $card;
    }

    // 企业微信
    public function tab6(){
        $formdata6 = Setting::getlists([],'qywx');
        $form6 = new WidgetsForm($formdata6);
        $form6->action('settings/save');
        $form6->confirm('确认已经填写完整了吗？');
        $form6->hidden('action_name')->value('qywx');
        $form6->html('<h3>企业微信</h3>');
        $form6->text('qywx_app_id','app_id')->required();
        $form6->text('qywx_secret','secret')->required();
        $form6->text('qywx_notify_url','异步通知地址')->placeholder('异步通知地址');
        $form6->disableResetButton();
        $card =  Card::make('企业微信参数配置',$form6);
        return $card;
    }
}
