<?php

namespace App\Merchant\Controllers\Wxgzh;

use App\Http\Controllers\Controller;
use App\Merchant\Actions\Form\ViewTiyanQrcodeForm;
use App\Merchant\Actions\Form\WxMinAppFabuForm;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\WxappConfig;
use App\Models\Hotel\WxopenMiniProgramOauth;
use App\Models\Hotel\WxopenMiniProgramVersion;
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
use Illuminate\Http\Request;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;
use App\Models\Hotel\HotelSetting;

// 微信公众号
class GzhController extends Controller {

    public $oauth;
    public function index(Content $content) {
        return $content
            ->header('公众号管理')
            ->description('公众号相关信息管理')
            ->breadcrumb(['text' => '公众号管理', 'uri' => ''])
            ->row(function(Row $row) {

                $row->column(2,  $this->cbox());

                $row->column(10, $this->pageMain());
            });
    }

    public function cbox($sc_id = 1){
        $nav_menu = [
            '1' => '基本信息',
            '2' => '菜单管理',
            '3' => '自回回复',
            '4' => '关注回复',
            '5' => '模板消息',
            //'6' => '粉丝列表',
        ];
        $datas = Request()->all();
        $hangzu_id = !empty($datas['hangzu_id'])? $datas['hangzu_id']:'';
        $hangzulist  = $nav_menu;
        $hzhtml = "<ul class='list-group list-group-flush'>";
        foreach ($hangzulist as $key => $items){
            $class = '';
            if(!empty($hangzu_id)){
                if($key == $hangzu_id){
                    $class = 'class="text-danger"';
                }
            }else{
                if($key == 1){
                    $class = 'class="text-danger"';
                }
            }

            $hzhtml .= '<li class="list-group-item"><a '.$class.' href="/merchant/wxgzh?&hangzu_id='.$key.'">'.$items.'</a></li>';
        }
        //$hzhtml .= '<li class="list-group-item"><a href="/merchant/wxgzh?&sc_id='.$sc_id.'" target="_blank">水电费</a></li>';
        $hzhtml .= '</ul>';
        $box = new Box('操作项', $hzhtml);
        //$box->collapsable();
        return $box;
    }

    // 页面
    public function pageMain() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'wxgzh'])->first();
        $this->oauth = $oauth;
        $data = [];
        $tab  = Tab::make();
        $datas = Request()->all();
        $hangzu_id = !empty($datas['hangzu_id'])? $datas['hangzu_id']:'';
        if(!empty($oauth->id)){
            if($hangzu_id == 1 || $hangzu_id == ''){
                $tab->add('微信公众号[授权模式]', $this->tab1());
            }
            if($hangzu_id == 2){
                $tab->add('菜单管理', $this->tab2());
            }
            if($hangzu_id == 3){
                $tab->add('自回回复', $this->tab3());
            }
            if($hangzu_id == 4){
                $tab->add('关注回复', $this->tab4());
            }
            if($hangzu_id == 5){
                $tab->add('模板消息', $this->tab5());
            }
            /*if($hangzu_id == 6){
                $tab->add('粉丝列表', $this->tab6());
            }*/
        }else{
            $tab->add('微信公众号[授权模式]', $this->tab1());
        }

        return $tab->withCard();
    }

    public function tab1() {

        $oauth        = $this->oauth;//WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'wxgzh'])->first();
        $openPlatform = wxopen();

        if (empty($oauth->hotel_id)) {
            if (empty(Admin::user()->hotel_id)) {
                return $card = Card::make('授权异常', '请先完成酒店资料填写 >>> <a href="' . url('merchant/storeinfo') . '"> 前往填写 </a>');
            }
            $url   = $openPlatform->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?app_type=wxgzh&uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],1);
            $htmls = '<a target="_blank" href="' . $url . '"> >>> 去授权</a>  ';
            $htmls .= '<button type="button" class="btn btn-primary text-capitalize" onclick="window.location.reload()"><i class="feather icon-refresh-ccw"></i> 刷新 </button>';
            return $card = Card::make('扫码授权', $htmls);
        } else {
            //$auth_url          = app('wechat.open')->inits()->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],1,$oauth->AuthorizerAppid);
            $htmls = '<br/><a class="btn btn-white btn-outline" target="_blank" href="' . admin_url('/viewWxgzhInfo') . '"><i class="feather icon-alert-circle"></i> 查看公众号信息</a>';
            $tips_html = '<span class="text-danger">已授权</span> 如果需要重新授权 >>> <a target="_blank" href="' . url('/merchant/gzh-afreshOauth') . '"> 点击</a> ';
            $alert     = Alert::make($tips_html, '说明')->info();
            $info = $this->viewWxgzhInfo();
            $card      = Card::make('授权信息', $alert.$info);
            return $card;
        }
    }

    public function tab2() {
        // 菜单管理
        $grid = '';

        //$res = $wxOpen->hotelWxgzh(Admin::user()->hotel_id)->menu->current();
        $oauth =$this->oauth;
        $miniprogram_appid        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'minapp'])->value('AuthorizerAppid');

        $card1 = Card::make('', admin_view('merchant.wxgzh-menu',['oauth'=>$oauth,'miniprogram_appid'=> $miniprogram_appid]));
        return $card1;
    }
    public function tab3() {
        // 模板消息
        $form1 = new WidgetsForm();
        $form1->text('msgtpl1', '订房成功提醒')->help('类目: 服装/鞋/箱包');
        /*$form1->text('msgtpl2', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl3', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl4', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl5', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl6', '下单成功提醒(类目: 服装/鞋/箱包 )');*/
        $form1->disableResetButton();

        $card1 = Card::make('', '');
        return $card1;
    }
    public function tab4() {
        // 模板消息
        $form1 = new WidgetsForm();
        $form1->text('msgtpl1', '订房成功提醒')->help('类目: 服装/鞋/箱包');
        /*$form1->text('msgtpl2', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl3', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl4', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl5', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl6', '下单成功提醒(类目: 服装/鞋/箱包 )');*/
        $form1->disableResetButton();

        $card1 = Card::make('', '');
        return $card1;
    }

    public function tab5() {
        $openPlatform = app('wechat.open');
        $wxgzh = $openPlatform->hotelWxgzh(Admin::user()->hotel_id);

        //
        /*$res = $wxgzh->template_message->getIndustry();
        // 如果没有设置行业
        if(empty($res['primary_industry']['first_class'])){
            $wxgzh->template_message->setIndustry(11,12); // 酒店,旅游
        }

        // 获取模板列表
        $list = $wxgzh->template_message->getPrivateTemplates();*/


        // 模板消息
        $field = [
            'booking_gzh_msg_tpl_success',
            'booking_gzh_msg_tpl_cancel',
            'booking_gzh_msg_tpl_fail',
        ];
        $formdata = HotelSetting::getlists($field,Admin::user()->hotel_id);

        $form1 = new WidgetsForm($formdata);
        $form1->action('hotel-setting-edit');
        $form1->confirm('确认已经填好了吗?');
        $form1->hidden('action_name')->value('gzh_msg_tpl');
        $form1->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form1->width(9,3);
        $form1->text('booking_gzh_msg_tpl_success', '酒店预订成功通知 模板ID:<br> booking_gzh_msg_tpl_success')->help('类目: 旅游服务,住宿服务')->required();
        $form1->text('booking_gzh_msg_tpl_cancel', '预订取消通知 模板ID:<br> booking_gzh_msg_tpl_cancel')->help('类目: 旅游服务,住宿服务')->required();
        $form1->text('booking_gzh_msg_tpl_fail', '预订失败通知 模板ID:<br> booking_gzh_msg_tpl_fail')->help('类目: 旅游服务,住宿服务')->required();
        $form1->disableResetButton();
        $tips_html = <<<HTML
        <ul>
        <li>以下公众号模板消息的设置来自于：类目（旅游服务,住宿服务）</li>
        <li><img data-action="preview-img" class="img img-thumbnail" src='/img-jiaocheng/gzh-tplmsg-01.png' width='160'></li>
        <li>公众号模板消息设置:<a href="https://mp.weixin.qq.com/advanced/tmplmsg?action=list&t=tmplmsg/list&new_type=4&token=229901907&lang=zh_CN" target="_blank"> >>>前往</a></li>
        </ul>
HTML;
        $alert     = Alert::make($tips_html, '说明')->info();

        $card1 = Card::make('', $alert.$form1);
        return $card1;
    }

    // 粉丝列表
    public function tab6() {
        //$wxOpen = app('wechat.open');
        //$gzh = $wxOpen->hotelWxgzh(Admin::user()->hotel_id);
        //$list = $gzh->user->list($nextOpenId = null);

        //$app = app('wechat.official_account');
        // 获取 用户列表
        //$list = $app->user->list($nextOpenId = null);
        // 获取单个粉丝信息
        //$list = $app->user->get('o5wESxMpb5jmFBa3lQYGAvkTpxpM');
        // 服务商
        // $list = $gzh->user->get('oRpZY6qR8tPqk_fQqks08YhcjBYY');


    }





    // 重新授权
    public function afreshOauth(Request $request) {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'wxgzh'])->first();
        $auth_url          = app('wechat.open')->inits()->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],1,$oauth->AuthorizerAppid);
        return redirect($auth_url);
    }

    // 查看公众号信息
    public function viewWxgzhInfo() {

        $wxOpen = app('wechat.open');
        $account_info = $wxOpen->getAuthorizer('',Admin::user()->hotel_id,'wxgzh');

        $result = $wxOpen->hotelWxgzh(Admin::user()->hotel_id)->qrcode->temporary('foo', 6 * 24 * 3600);
        $qrcode_url = '';
        if(!empty($result['url'])){
            $qrcode_url = $result['url'];
        }

        //$oauth_info = $wxOpen->getOauthInfo('',Admin::user()->hotel_id,'','wxgzh');
        //$menu  = $officialAccount->menu->list();
        //$minappinfo  = $officialAccount->account->getBasicInfo();
        $minappinfo = $account_info['authorizer_info'];

        $form        = new WidgetsForm();
        $form->disableSubmitButton();
        $form->disableResetButton();
        $form->disableSubmitButton();
        if (!empty($minappinfo['nick_name'])) {
            if(!empty($qrcode_url)){
                $form->html('<div style="margin-top: 0px;">'.\QrCode::size(100)->generate($qrcode_url).'</div>')->label('公众号二维码');
            }
            $form->text('appid', '公众号 appid')->default($this->oauth->AuthorizerAppid);
            $form->text('nickname', '公众号 名称')->default($minappinfo['nick_name']);
            $form->html('<div style="margin-top: 0px;"><img width="140" src="'.$minappinfo['head_img'].'" /></div>')->label('公众号 图标');
            //$form->image('head_image_url', '公众号 图标')->default($minappinfo['head_img']);
            //$form->image('qrcode_url', '公众号 二维码')->default($minappinfo['qrcode_url'])->width(3);
            $form->text('signature', '公众号 简介')->default($minappinfo['signature']);
            $form->text('principal_name', '主体公司')->default($minappinfo['principal_name']);
            //$form->radio('realname_status', '实名验证状态')->options(['1' => '已验证', '0' => '未验证'])->default($minappinfo['realname_status']);
            $form->html('<h3>微信认证信息</h3>');
            //$form->radio('naming_verify	', '年审认证')->options(['1' => '已认证', '' => '未认证'])->default($minappinfo['wx_verify_info']['naming_verify']);
            if(empty($minappinfo['wx_verify_info']['naming_verify'])){

            }

        } else {
            $form->html('<h3>未获取公众号信息</h3>');
        }
        $card = Card::make('', $form);
        return $card;
        return $content
            ->header('公众号 信息')
            ->description('基础信息')
            ->breadcrumb(['text' => '公众号 信息', 'uri' => ''])
            ->body($card);
    }

}
