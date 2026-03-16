<?php

namespace App\Admin\Controllers\Setting;

use App\Models\Hotel\Setting;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetsForm;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dcat\Admin\Widgets\Modal;

// 全局配置
class SettingsController extends Controller {
    public function index(Content $content) {
        return $content
            ->header('全局配置')
            ->description('')
            ->breadcrumb(['text' => '全局配置', 'uri' => ''])
            ->body($this->pageMain());
    }

    // 页面
    public function pageMain() {
        $req  = Request()->all();
        $type = request('_t', 1);
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('短信/邮件', $this->tab0(),false,'setting-0');
        $tab->add('资源上传', $this->tab1(),false,'setting-1');
        $tab->add('微信支付服务商', $this->tab2(),false,'setting-2');
        $tab->add('微信公众号', $this->tab3(),false,'setting-3');
        $tab->add('微信小程序', $this->tab4(),false,'setting-4');
        $tab->add('微信开放平台', $this->tab5(),false,'setting-5');
        $tab->add('企业微信', $this->tab6(),false,'setting-6');
        $tab->add('平台服务热线', $this->tab7(),false,'setting-7');
        $tab->add('数电发票', $this->tab8(),false,'setting-8');

        //$tab->add('子帐号',$this->tab3());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    // 短信-邮件
    public function tab0() {
        $formdata = Setting::getlists([], 'sms_mail');
        $form1    = new WidgetsForm($formdata);
        $form1->confirm('确认已经填写完整了吗？');
        $form1->action('settings/save');
        $form1->html('<h3>短信</h3>');
        $form1->radio('sms_channel', '短信通道')->options(['aliyun' => '阿里云'])->value('aliyun');
        $form1->hidden('action_name')->value('sms_mail');
        $form1->hidden('is_refresh')->value(1);
        $form1->text('sms_aliyun_key', 'key')->required();
        $form1->text('sms_aliyun_secret', 'secret')->required();
        $form1->text('sms_aliyun_sign', '短信签名')->required();
        $form1->table('sms_aliyun_template', '短信模板', function (NestedForm $table) {
            $table->text('sms_aliyun_key', '模板标识')->prepend('')->required();
            $table->text('sms_aliyun_template_id', '模板code')->prepend('')->required();
            $table->text('sms_aliyun_template_name', '模板名')->prepend('')->required();
            $table->text('sms_aliyun_template_contents', '模板内容')->prepend('')->required();
        });

        // 设置小程序隐私信息
        $form2 = new WidgetsForm();
        $form2->action('/yisiSettingSave');
        $form2->confirm('确认现在提交吗？');
        $form2->html('测试短信');
        $form2->disableResetButton();

        $modal = Modal::make()
            ->lg()
            ->title('测试短信')
            ->body($form2)
            ->button('<button class="btn btn-white btn-outline">测试短信</button>');

        $form1->html($modal);
        $form1->html('请先去 <a target="_blank" href="https://cn.aliyun.com/"> 阿里云 </a> 申请短信签名写短信模板，然后拿回信息填在这里');
        $form1->divider();
        $form1->html('<h3>邮件配置</h3>');
        $form1->text('mail_host', 'SMTP 服务器')->required();
        $form1->text('mail_port', '端口')->help('只是纯数字.例：475')->required();
        $form1->radio('mail_encryption', '安全')->options(['1' => '无', '2' => 'SSL', '3' => 'TLS'])->value('1');
        $form1->text('mail_username', '账号')->required();
        $form1->text('mail_password', '密码')->required();
        $form1->text('mail_from_address', '发件人名称')->required();
        $form1->text('mail_from_test', '测试收件箱');
        $form1->table('mail_template', '邮件内容模板', function (NestedForm $table) {
            $table->text('mail_template_name', '模板名')->prepend('')->required();
            $table->textarea('mail_template_contents', '模板内容')->required();
        });
        $form1->disableResetButton();
        $card = Card::make('短信/邮件', $form1);
        return $card;
    }

    public function tab1() {
        $formdata = Setting::getlists([], 'oss');
        $form     = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('oss');
        //$form->html($alert = Alert::make('内容', '提示')->success());
        $form->radio('oss_ossType', '默认上传方式')->options(['1' => '阿里云oss'])->value('1');
        /*$form->text('oss_bucket', '存储空间名称 Bucket')->help('注意：这里不要填-appid，点击查看教程')->width(140)->required();
        $form->text('oss_region', '所属地域 Region')->help('')->width(140)->placeholder('')->required();
        $form->text('oss_appId', 'AppId')->help('')->width(140)->placeholder('');
        $form->text('oss_secretId', 'SecretId')->help('<a href="https://img.mini.chongyeapp.com/images/appsecret.png" target="_blank">查看说明</a>')->width(140)->placeholder('');
        $form->password('oss_secretKey', 'SecretKey')->width(140)->placeholder('');
        $form->url('oss_domain', 'Domain')->help('请补全http:// 或 https://，例如：http://img.chongyeapp.com')->width(140)->placeholder('');
        */$form->disableResetButton();
        $card = Card::make('上传存储配置', $form);
        return $card;
    }

// 微信支付服务商
    public function tab2() {
        $formdata = Setting::getlists([], 'pay_isv');
        $form     = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('pay_isv');
        $form->html('<h3>[' . env('APP_NAME') . '] 微信支付服务商</h3>');
        $form->text('isv_app_id', 'app_id')->required();
        $form->text('isv_secret', 'secret')->required();
        $form->text('isv_mch_id', '服务商 商户号')->required();
        $form->text('isv_key', 'api key');
        $form->text('isv_cert_path', '证书 cert_path');
        $form->text('isv_key_path', '证书key_path');
        $form->text('isv_platform_pub_id', '微信支付公钥ID');
        $form->text('isv_platform_pub_cert', '微信支付公钥')->help('公钥文件路径');
        $form->text('isv_notify_url', '异步通知地址');
        $form->disableResetButton();
        $card = Card::make('支付服务商参数', $form);
        return $card;
    }

    // 微信公众号
    public function tab3() {
        $formdata = Setting::getlists([], 'wx_gzh');
        $form     = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wx_gzh');
        $form->html('<h3>[' . env('APP_NAME') . '] 微信公众号</h3>');
        $form->text('gzh_app_id', 'app_id')->required();
        $form->text('gzh_secret', 'secret')->required();
        $form->text('gzh_notify_url', '异步通知地址')->placeholder('');
        $form->text('gzh_Token', 'Token')->placeholder('');
        $form->text('gzh_aesKey', 'aesKey')->placeholder('');
        $form->text('gzh_staff', 'staff')->placeholder('');
        $form->text('gzh_wxRobot', 'wxRobot')->placeholder('');
        $form->html('温馨提示：获取前请先确认您已获得模板消息的使用权限，并且模板消息中没有任何数据。获取后请不要到公众号后台 删除相应的模板消息，否则会影响模板消息正常使用。');
        $form->table('gzh_template', '模板消息', function (NestedForm $table) {
            $table->text('gzh_template_key', '模板字段')->prepend('')->required();
            $table->text('gzh_template_name', '模板名')->prepend('')->required();
            $table->text('gzh_template_id', '模板ID')->prepend('')->required();
        });
        $form->disableResetButton();
        $card = Card::make('公众号参数配置', $form);
        return $card;
    }

    // 微信小程序
    public function tab4() {
        $formdata = Setting::getlists([], 'wxmin');
        $form     = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wxmin');
        $form->html('<h3>[' . env('APP_NAME') . '] 微信小程序</h3>');
        $form->text('wxmin_app_id', 'app_id')->required();
        $form->text('wxmin_secret', 'secret')->required();
        $form->text('wxmin_notify_url', '异步通知地址')->placeholder('');
        $form->text('wxmin_Token', 'Token')->placeholder('');
        $form->text('wxmin_aesKey', 'aesKey')->placeholder('');
        $form->text('wxmin_staff', 'staff')->placeholder('');
        $form->text('wxmin_wxRobot', 'wxRobot')->placeholder('');
        $form->html('温馨提示：获取前请先确认您已获得模板消息的使用权限，并且模板消息中没有任何数据。获取后请不要到公众号后台 删除相应的模板消息，否则会影响模板消息正常使用。');
        $form->table('wxmin_template', '订阅消息', function (NestedForm $table) {
            $table->text('wxmin_template_name', '消息模板名')->prepend('')->required();
            $table->text('wxmin_template_id', '消息ID')->prepend('')->required();
        });
        $form->disableResetButton();
        $card = Card::make('微信小程序参数配置', $form);
        return $card;
    }

    // 微信开放平台
    public function tab5() {
        $formdata = Setting::getlists([], 'wxopen');
        $form     = new WidgetsForm($formdata);
        $form->action('settings/save');
        $form->confirm('确认已经填写完整了吗？');
        $form->hidden('action_name')->value('wxopen');
        $form->html('<h3>[' . env('APP_NAME') . '] 微信开放平台</h3>');
        $form->text('wxopen_app_id', 'app_id')->required();
        $form->text('wxopen_secret', 'secret')->required();
        $form->text('wxopen_Token', 'Token')->required();
        $form->text('wxopen_aesKey', 'aesKey')->required();
        $form->text('wxopen_oauth_url', '授权回调地址')->placeholder('异步通知地址');
        $form->text('wxopen_notify_url', '消息通知地址')->placeholder('异步通知地址');

        $form->disableResetButton();
        $card = Card::make('微信开放平台参数配置', $form);
        return $card;
    }

    // 企业微信
    public function tab6() {
        $formdata6 = Setting::getlists([], 'qywx');
        $form6     = new WidgetsForm($formdata6);
        $form6->action('settings/save');
        $form6->confirm('确认已经填写完整了吗？');
        $form6->hidden('action_name')->value('qywx');
        $form6->html('<h3>[' . env('APP_NAME') . '] 企业微信</h3>');
        $form6->text('qywx_app_id', 'app_id')->required();
        $form6->text('qywx_secret', 'secret')->required();
        $form6->text('qywx_notify_url', '异步通知地址')->placeholder('异步通知地址');
        $form6->disableResetButton();
        $card = Card::make('企业微信参数配置', $form6);
        return $card;
    }

    // 平台服务热线
    public function tab7() {
        $formdata7 = Setting::getlists([], 'platform_fuwu_info');
        $form7     = new WidgetsForm($formdata7);
        $form7->action('settings/save');
        $form7->confirm('确认已经填写完整了吗？');
        $form7->hidden('action_name')->value('platform_fuwu_info');

        $form7->text('fuwu_phone', '服务电话')->required();
        $form7->text('fuwu_email', '服务邮箱')->required();
        $form7->image('fuwu_wx', '企业客服微信')
            ->width(3)
            ->url('/upload/storage')
            //->dimensions(['width' => 200, 'height' => 273])
            ->removable(false)
            ->saveFullUrl()
            ->autoUpload()
            ->autoSave(false)
            ->help('图片尺寸:200*273')
            ->required();
        $form7->disableResetButton();
        $card = Card::make('平台服务热线', $form7);
        return $card;
    }

    // 数电发票
    public function tab8() {
        $formdata8 = Setting::getlists([], 'invoice_configs');
        $form8     = new WidgetsForm($formdata8);
        $form8->action('settings/save');
        $form8->confirm('确认已经填写完整了吗？');
        $form8->hidden('action_name')->value('invoice_configs');

        $form8->html('温馨提示：正式开票后，请小心选择开票环境');

        $form8->radio('default_debug', '环境选择')->options(['debug' => '测试', 'live' => '正式'])->required();

        $form8->html('<h4>测试环境配置</h4>');
        $form8->text('debug_appKey', '测试 第三方应用 APP Key')->required();
        $form8->text('debug_appSecret', '测试 第三方应用 App Secret')->required();
        $form8->text('debug_apiUrl', '测试 api请求地址')->required();
        $form8->text('debug_callbackurl', '测试 异步通知地址')->required();

        $form8->html('<h4>正式环境配置</h4>');
        $form8->text('live_appKey', '第三方应用 APP Key')->required();
        $form8->text('live_appSecret', '第三方应用 App Secret')->required();
        $form8->text('live_apiUrl', 'api请求地址')->required();
        $form8->text('live_callbackurl', '异步通知地址')->required();


        $form8->disableResetButton();
        $card = Card::make('数电发票第三方应用配置', $form8);
        return $card;
    }

    /**
     * @desc 参数存改主入口
     */
    public function formSave(Request $request) {
        $action_name = $request->get('action_name');
        $validator   = \Validator::make($request->all(), [
            'action_name' => 'required',
        ], [
            'action_name.required' => '操作项 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->all()[0]);
        }
        // 根据操作名不同 调用不用的函数做参数验证，存改
        switch ($action_name) {
            case 'sms_mail':
                return $this->sms_mail($request);
                break;
            case 'oss':
                return $this->oss($request);
                break;
            case 'pay_isv':
                return $this->pay_isv($request);
                break;
            case 'wx_gzh':
                return $this->wx_gzh($request);
                break;
            case 'wxmin':
                return $this->wxmin($request);
                break;
            case 'wxopen':
                return $this->wxopen($request);
                break;
            case 'qywx':
                return $this->qywx($request);
                break;
            case 'booking_switch':
                return $this->booking_switch($request);
                break;
            case 'platform_fuwu_info':
                return $this->platform_fuwu_info($request);
                break;
            case 'invoice_configs':
                return $this->invoice_configs($request);
                break;
            default:
                break;
        }
        return (new WidgetsForm())->response()->error('保存遇到问题,请检查');
    }

    // 订房通知对象配置
    public function sms_mail(Request $request) {
        $validator = \Validator::make($request->all(), [
            'sms_aliyun_key'    => 'required',
            'sms_aliyun_secret' => 'required',
            'sms_aliyun_sign'   => 'required',
            'mail_host'         => 'required',
            'mail_port'         => 'required|numeric',
            //'mail_encryption' => 'required',
            'mail_username'     => 'required|email',
            'mail_password'     => 'required',
            'mail_from_address' => 'required',
            'mail_from_test'    => 'nullable|email',
        ], [
            'sms_aliyun_key.required'    => '阿里云短信key 不能为空',
            'sms_aliyun_secret.required' => '阿里云短信secret 不能为空',
            'sms_aliyun_sign.required'   => '阿里云短信sign签名 不能为空',
            //'booking_notify_gzh_open_id.required' => '',
            'mail_host.required'         => '邮件服务器 不能为空',
            'mail_port.required'         => '邮件服务器端口 不能为空',
            'mail_port.numeric'          => '邮件服务器端口 只能是数字',
            'mail_username.required'     => '邮件账号名 不能为空',
            'mail_password.required'     => '邮件账号密码 不能为空',
            'mail_from_address.required' => '邮件发件人名称 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'sms_aliyun_key'    => $request->sms_aliyun_key,
            'sms_aliyun_secret' => $request->sms_aliyun_secret,
            'sms_aliyun_sign'   => $request->sms_aliyun_sign,
            'mail_host'         => $request->mail_host,
            'mail_port'         => $request->mail_port,
            'mail_encryption'   => $request->mail_encryption,
            'mail_username'     => $request->mail_username,
            'mail_password'     => $request->mail_password,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_test'    => $request->mail_from_test,
        ];
        $insdata = array_filter($insdata); // 过滤掉空值
        if (!empty($request->sms_aliyun_template)) {
            $insdata['sms_aliyun_template'] = json_encode($request->sms_aliyun_template, JSON_UNESCAPED_UNICODE);
        }
        if (!empty($request->mail_template)) {
            $insdata['mail_template'] = json_encode($request->mail_template, JSON_UNESCAPED_UNICODE);
        }
        $sts = Setting::createRow($insdata, 'sms_mail');
        if ($request->is_refresh == '1') {
            return JsonResponse::make()->data($request->all())->success('成功！')->refresh();
        } else {
            return JsonResponse::make()->data($request->all())->success('成功！');
        }

    }

    public function booking_switch(Request $request) {

    }

    // 上传配置
    public function oss(Request $request) {
        $validator = \Validator::make($request->all(), [
            'oss_ossType'   => 'required',
            'oss_bucket'    => 'required',
            'oss_region'    => 'required',
            'oss_appId'     => 'required',
            'oss_secretId'  => 'required',
            'oss_secretKey' => 'required',
            'oss_domain'    => 'required',
        ], [
            'oss_ossType.required'  => '请选择上传方式',
            'oss_bucket.required'   => '存储空间 不能为空',
            'oss_region.required'   => '所属地域 不能为空',
            'oss_appId.required'    => 'appId 不能为空',
            'oss_secretId.required' => 'secretId 不能为空',
            'oss_secretKey.numeric' => 'secretKey 只能是数字',
            'oss_domain.required'   => 'domain 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }

        $insdata = [
            'oss_ossType'   => $request->oss_ossType,
            'oss_bucket'    => $request->oss_bucket,
            'oss_region'    => $request->oss_region,
            'oss_appId'     => $request->oss_appId,
            'oss_secretId'  => $request->oss_secretId,
            'oss_secretKey' => $request->oss_secretKey,
            'oss_domain'    => $request->oss_domain,
        ];
        $sts     = Setting::createRow($insdata, 'oss');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 微信支付服务商
    public function pay_isv(Request $request) {
        $validator = \Validator::make($request->all(), [
            'isv_app_id'     => 'required',
            'isv_secret'     => 'required',
            'isv_mch_id'     => 'required|numeric',
            'isv_notify_url' => 'required',
        ], [
            'isv_app_id.required'     => 'app_id 不能为空',
            'isv_secret.required'     => 'secret 不能为空',
            'isv_mch_id.required'     => '商户号 不能为空',
            'isv_notify_url.required' => '异步通知地址 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }

        $insdata = [
            'isv_app_id'     => $request->isv_app_id,
            'isv_secret'     => $request->isv_secret,
            'isv_mch_id'     => $request->isv_mch_id,
            'isv_notify_url' => $request->isv_notify_url,
            'isv_key'        => $request->isv_key,
            'isv_cert_path'  => $request->isv_cert_path,
            'isv_key_path'   => $request->isv_key_path,
            'isv_platform_pub_id' => $request->isv_platform_pub_id,
            'isv_platform_pub_cert' => $request->isv_platform_pub_cert,
        ];
        $sts     = Setting::createRow($insdata, 'pay_isv');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    public function wx_gzh(Request $request) {
        $validator = \Validator::make($request->all(), [
            'gzh_app_id' => 'required',
            'gzh_secret' => 'required',
            //'gzh_notify_url' => 'required',
            //'gzh_Token'      => 'required',
            //'gzh_aesKey'     => 'required',
            //'gzh_staff'      => 'required',
            //'gzh_wxRobot'    => 'required',
        ], [
            'gzh_app_id.required'     => 'app_id 不能为空',
            'gzh_secret.required'     => 'secret 不能为空',
            'gzh_notify_url.required' => '异步通知地址 不能为空',
            'gzh_Token.required'      => '异步通知地址 不能为空',
            'gzh_aesKey.required'     => '异步通知地址 不能为空',
            'gzh_staff.required'      => '异步通知地址 不能为空',
            'gzh_wxRobot.required'    => '异步通知地址 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'gzh_app_id'     => $request->gzh_app_id,
            'gzh_secret'     => $request->gzh_secret,
            'gzh_notify_url' => $request->gzh_notify_url,
            'gzh_Token'      => $request->gzh_Token,
            'gzh_aesKey'     => $request->gzh_aesKey,
            'gzh_staff'      => $request->gzh_staff,
            'gzh_wxRobot'    => $request->gzh_wxRobot
        ];
        $insdata = array_filter($insdata);
        if (!empty($request->gzh_template)) {
            $gzh_template = $request->gzh_template;
            foreach ($gzh_template as $key => $items) {
                if(!empty($items['_remove_'])){
                    unset($gzh_template[$key]);
                }
            }
            $insdata['gzh_template'] = json_encode($gzh_template, JSON_UNESCAPED_UNICODE);
        }
        $sts = Setting::createRow($insdata, 'wx_gzh');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    //  小程序配置
    public function wxmin(Request $request) {
        $validator = \Validator::make($request->all(), [
            'wxmin_app_id' => 'required',
            'wxmin_secret' => 'required',
            /*'wxmin_notify_url' => 'required',
            'wxmin_Token'      => 'required',
            'wxmin_aesKey'     => 'required',*/
            //'gzh_staff'      => 'required',
            //'gzh_wxRobot'    => 'required',
        ], [
            'wxmin_app_id.required'     => 'app_id 不能为空',
            'wxmin_secret.required'     => 'secret 不能为空',
            'wxmin_notify_url.required' => '异步通知地址 不能为空',
            'wxmin_Token.required'      => '异步通知地址 不能为空',
            'wxmin_aesKey.required'     => '异步通知地址 不能为空',
            'wxmin_staff.required'      => '异步通知地址 不能为空',
            'wxmin_wxRobot.required'    => '异步通知地址 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'wxmin_app_id'     => $request->wxmin_app_id,
            'wxmin_secret'     => $request->wxmin_secret,
            'wxmin_notify_url' => $request->wxmin_notify_url,
            'wxmin_Token'      => $request->wxmin_Token,
            'wxmin_aesKey'     => $request->wxmin_aesKey,
            'wxmin_staff'      => $request->wxmin_staff,
            'wxmin_wxRobot'    => $request->wxmin_wxRobot,
        ];
        $insdata = array_filter($insdata);
        if (!empty($request->wxmin_template)) {
            $insdata['wxmin_template'] = json_encode($request->wxmin_template, JSON_UNESCAPED_UNICODE);
        }
        $sts = Setting::createRow($insdata, 'wxmin');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 微信开放平台
    public function wxopen(Request $request) {
        $validator = \Validator::make($request->all(), [
            'wxopen_app_id' => 'required',
            'wxopen_secret' => 'required',
            //'wxopen_notify_url'    => 'required',
        ], [
            'wxopen_app_id.required'     => 'app_id 不能为空',
            'wxopen_secret.required'     => 'secret 不能为空',
            'wxopen_notify_url.required' => '异步通知地址 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }

        $insdata = [
            'wxopen_app_id'     => $request->wxopen_app_id,
            'wxopen_secret'     => $request->wxopen_secret,
            'wxopen_Token'      => $request->wxopen_Token,
            'wxopen_aesKey'     => $request->wxopen_aesKey,
            'wxopen_oauth_url'  => $request->wxopen_oauth_url,
            'wxopen_notify_url' => $request->wxopen_notify_url,
        ];
        $insdata = array_filter($insdata);
        $sts     = Setting::createRow($insdata, 'wxopen');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }


    public function platform_fuwu_info(Request $request) {
        $validator = \Validator::make($request->all(), [
            'fuwu_phone' => 'required',
            'fuwu_email' => 'required|email',
            'fuwu_wx'    => 'required|url',
        ], [
            'fuwu_phone.required' => '服务电话 不能为空',
            'fuwu_email.required' => '服务邮箱 不能为空',
            'fuwu_email.email'    => '服务邮箱 格式不正确',
            'fuwu_wx.required'    => '企业客服微信 不能为空',
            'fuwu_wx.url'         => '企业客服微信 不是有效图片链接',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'fuwu_phone' => $request->fuwu_phone,
            'fuwu_email' => $request->fuwu_email,
            'fuwu_wx'    => $request->fuwu_wx,
        ];
        $insdata = array_filter($insdata);
        $sts     = Setting::createRow($insdata, 'platform_fuwu_info');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    // 数电发票配置
    public function invoice_configs(Request $request) {
        $validator = \Validator::make($request->all(), [
            'default_debug'     => 'required',
            'debug_appKey'      => 'required',
            'debug_appSecret'   => 'required',
            'debug_apiUrl'      => 'required|url',
            'debug_redirectUri' => 'required|url',
            'live_appKey'       => 'required',
            'live_appSecret'    => 'required',
            'live_apiUrl'       => 'required|url',
            'live_redirectUri'  => 'required|url',
        ], [
            'default_debug.required'     => '环境选择 不能为空',
            'debug_appKey.required'      => '测试 appKey 不能为空',
            'debug_appSecret.required'   => '测试 appSecret 不能为空',
            'debug_apiUrl.required'      => '测试 api请求地址 不能为空',
            'debug_apiUrl.url'           => '测试 api请求地址 不是有效链接',
            'debug_redirectUri.required' => '测试 异步通知地址 不能为空',
            'debug_redirectUri.url'      => '测试 异步通知地址 不是有效链接',
            'live_appKey.required'       => '正式 appKey 不能为空',
            'live_appSecret.required'    => '正式 appSecret 不能为空',
            'live_apiUrl.required'       => '正式 api请求地址 不能为空',
            'live_apiUrl.url'            => '正式 api请求地址 不是有效链接',
            'live_redirectUri.required'  => '正式 异步通知地址 不能为空',
            'live_redirectUri.url'       => '正式 异步通知地址 不是有效链接',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $insdata = [
            'default_debug'     => $request->default_debug,
            'debug_appKey'      => $request->debug_appKey,
            'debug_appSecret'   => $request->debug_appSecret,
            'debug_apiUrl'      => $request->debug_apiUrl,
            'debug_redirectUri' => $request->debug_redirectUri,
            'live_appKey'       => $request->live_appKey,
            'live_appSecret'    => $request->live_appSecret,
            'live_apiUrl'       => $request->live_apiUrl,
            'live_redirectUri'  => $request->live_redirectUri,
        ];
        $insdata = array_filter($insdata);
        $sts     = Setting::createRow($insdata, 'invoice_configs');
        return JsonResponse::make()->data($request->all())->success('成功！');
    }

    public function qywx(Request $request) {
        return JsonResponse::make()->data($request->all())->error('暂不支持');
    }
}
