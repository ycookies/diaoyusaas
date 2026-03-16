<?php

namespace App\Merchant\Controllers;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Form;
use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use App\Models\Hotel\HotelSetting;
use App\Models\Hotel\Seller as SellerModels;
use Dcat\Admin\Widgets\Alert;

// 系统配置
class SysconfigController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('全局配置')
            ->description('')
            ->breadcrumb(['text'=> '全局配置','uri'=> ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain(){
        $req = Request()->all();
        $type = request('_t', 1);
        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('常规设置',$this->tab01(),'','_general_settings');
        $tab->add('订房相关',$this->tab0(),'','_booking');
        $tab->add('通知',$this->tab1(),'','_notive');
        $tab->add('推广海报',$this->tab2(),'','_tuigang');
        $tab->add('充值套餐',$this->tab3(),'','_chongzhi');
        $tab->add('新用户/邀请好友',$this->tab4(),'','_share');
        //$tab->add('密码修改', $this->tab2());
        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }
    // 常规设置
    public function tab01(){
        $flds     = [
            'user_regiser_required_wxcard',
            'user_update_info_required'
        ];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);

        //$form = Form::make(new HotelSetting())->edit(); // 暂时放弃
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('general_settings');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->switch('user_regiser_required_wxcard','注册必须开卡')
            ->width(5)
            ->help('新用户注册必须开卡（微信会员卡）')
            ->required();
        $form->switch('user_update_info_required','更新个人资料')->help('新用户注册登陆时必须更新个人资料')
            ->required();
        $card =  Card::make('',$form);
        return $card;
    }

    // 订房相关
    public function tab0(){
        $flds     = [
            'booking_full_status',
            'is_cancelling_verify',
            'cancelling_time',
            'exceed_cancelling_time_rate_24',
            'exceed_cancelling_time_rate_48',
            'vip_cancelling_time',
            'vip_exceed_cancelling_time_rate_24',
            'vip_exceed_cancelling_time_rate_48',
        ];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);

        //$form = Form::make(new HotelSetting())->edit(); // 暂时放弃
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('booking_configs');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->switch('booking_full_status','订房全局关闭')->help('<span class="text-danger">请谨慎操作.关闭后小程序不能定房，全面显示已满房</span>');
        $form->text('booking_wait_pay_time','订房未支付超时时间')->width(5)->default(10)->help('默认:10分钟,订单超过10分钟未支付,自动取消')->append('分钟');
        $form->html('<h4>退订&取消</h4>');
        $form->radio('is_cancelling_verify','退订审核')
            ->options([
                '0'=> '不审核',
                '1' => '开启审核'
            ])->required()
            ->help('');
        $form->html('<h4>普卡会员 退订&取消</h4>');
        $form->radio('cancelling_time','客房退订设置')
            ->options([
                '0'=> '无限制预订取消',
                '24'=> '入住日期前24小时',
                '48' => '入住日期前48小时',
                '1' => '不可取消'
            ])->required()
            ->help('配置退订规则');
        $form->rate('exceed_cancelling_time_rate_24', '24小时收取费用')->help('百分比,如：预订金额的5%')->width('150');
        $form->rate('exceed_cancelling_time_rate_48', '48小时收取费用')->help('百分比,如：预订金额的5%')->width('150');

        $form->html('<h4>付费VIP会员 退订&取消</h4>');
        $form->radio('vip_cancelling_time','付费VIP会员 客房退订设置')
            ->options([
                '0'=> '无限制预订取消',
                '24'=> '入住日期前24小时',
                '48' => '入住日期前48小时',
                '1' => '不可取消'
            ])->required()
            ->help('配置 付费VIP会员 退订规则');
        $form->rate('vip_exceed_cancelling_time_rate_24', '24小时收取费用')->help('百分比,如：预订金额的5%')->width('150');
        $form->rate('vip_exceed_cancelling_time_rate_48', '48小时收取费用')->help('百分比,如：预订金额的5%')->width('150');
        //$form->select('non_cancelling_time','不可取消时间')->options(SellerModels::$non_cancelling_time_arr)->required();
        /*        $form->number('max_booking_days_num','用户可预定最大天数')->help('')->help('用户最多可预定多少天内的房间.默认<span class="text-danger">30</span>天')->placeholder('可预定的最大天数')->default(30);
        $form->number('max_room_price_set_num','酒店客房维护的最大天数')->help('')->help('酒店运营最多可设置多少天内的房价,默认<span class="text-danger">60</span>天')->placeholder('房价维护的最大天数')->default(60);*/

        $form->disableResetButton();
        $card =  Card::make('订房综合相关',$form);
        return $card;
    }
    // 短信
    public function tab1(){
        $flds     = ['booking_notify_phone', 'booking_notify_gzh_open_id','booking_notify_qywx_robot_url','booking_notify_mail','kefu_center_qywx_robot_url'];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('booking_notify');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->text('booking_notify_phone','接受订房通知短信的手机号')->help('<span class="text-success">如需要提醒多人,请逗号分隔.</span><span class="text-danger">最多3个手机号</span><br/>短信内容:XXXX酒店,有客人订房:豪华大床房-2天-订单号:BXXXXX,请及时确认')->placeholder('例:176********,189********')->required();
        $form->text('booking_notify_gzh_open_id','接受订房公众号通知')->help('点击 <a href="/merchant/auth/setting" target="_blank"> 扫码绑定微信 </a> 需关注['.env('APP_NAME').']公众号')->placeholder('公众号的用户ID');
        $form->text('booking_notify_qywx_robot_url','接受订房通知的企业微信群')->help('添加 微信群通知机器人URL地址')->placeholder('填写 微信群通知机器人URL地址');
        $form->text('booking_notify_mail','接受订房通知的邮箱')->help('<span class="text-success">如需要提醒多人,请逗号分隔</span>')->placeholder('例:36648**@qq.com,18989**@163.com');
        $form->text('kefu_center_qywx_robot_url','接受小程序,公众号客服消息 微信群')->help('添加 微信群通知机器人URL地址')->placeholder('填写 微信群通知机器人URL地址');

        $form->disableResetButton();
        $card =  Card::make('接收订房通知配置',$form);
        return $card;
    }
    // 推广海报
    public function tab2(){
        $flds     = ['share_poster_bg_img'];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        //$form = Form::make(new HotelSetting());
        //$form->setResource()
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('share_poster');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $form->image('share_poster_bg_img','背景图')
            ->url('/upload/storage')
            ->dimensions(['width' => 600, 'height' => 1065])
            ->default(env('APP_URL').'/img/share/share4.png')
            ->removable(false)
            ->saveFullUrl()
            ->autoUpload()
            ->autoSave(false)
            ->help('图片尺寸:600*1065,请保留停放二维码的位置不变')
            ->required();

        $form->disableResetButton();
        $card =  Card::make('推广海报设置',$form);
        return $card;
    }

     // 充值套餐
    public function tab3(){

        $flds     = ['recharge_package_list'];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        //$form = Form::make(new HotelSetting());
        //$form->setResource()
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('recharge_package');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $tips_html = ' <ul><li>必须全部是整数值</li><li>充值套餐从小到大的顺序</li></ul>';
        $alert     = Alert::make($tips_html, '提示')->info();
        $form->html($alert);
        $form->table('recharge_package_list','充值套餐',function ($table) {
            $table->text('cost','充值金额(元)');
            $table->text('give_price','赠送金额(元)');
            $table->text('give_point','赠送积分');
        })->help('必须全部是整数值');
        $card =  Card::make('',$form);
        return $card;
    }

    // 新用户/邀请好友
    public function tab4(){
        $flds     = [
            'user_card_kaika_point',
            'user_booking_point',
            'user_pingjia_point',
            'user_share_valid_days',
            'user_share_balance',
        ];
        $formdata = HotelSetting::getlists($flds,Admin::user()->hotel_id);
        $form = new WidgetsForm($formdata);
        //$form = Form::make(new HotelSetting());
        //$form->setResource()
        $form->action('hotel-setting-edit');
        $form->confirm('确认已经填好了吗?');
        $form->hidden('action_name')->value('user_reward_config');
        $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
        $tips_html = ' <ul><li>请配置完整</li></ul>';
        $alert     = Alert::make($tips_html, '提示')->info();
        $form->html($alert);
        $form->html('<h3>奖励积分</h3>');
        $form->number('user_card_kaika_point','用户注册新开会员普卡')->help('成功开卡时发放');
        $form->number('user_booking_point','用户成功预订酒店')->help('在订单时离店发放');
        $form->number('user_pingjia_point','用户住店给予评价')->help('住店评价后发放');
        $form->divider();
        $form->html('<h3>奖励余额</h3>');
        $form->number('user_share_valid_days','用户推广邀请好友奖励条件')->default(30)->help('默认：30天,邀请后多少天内预订本酒店');
        $form->currency('user_share_balance','用户推广邀请好友成功住店')->symbol('￥')->default(20)->help('默认：20元 ,满足条件 奖励在订单离店后发放');

        $card =  Card::make('',$form);
        return $card;
    }

}
