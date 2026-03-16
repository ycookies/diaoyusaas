<?php

namespace App\Merchant\Controllers\WxMinapp;

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
// 小程序
class MinappListViewController extends Controller {

    public $oauth;
    public $nav_menu = [
        '1' => '基本信息',
        '2' => '版本管理',
        '3' => '支付配置',
        '4' => '成员管理',
        '5' => '模板消息',
        '6' => '类目管理',
    ];

    public function index(Content $content) {
        return $content
            ->header('小程序管理')
            ->description('微信小程序')
            ->breadcrumb(['text' => '小程序管理', 'uri' => ''])
            ->row(function(Row $row) {

                $row->column(2,  $this->cbox());

                $row->column(10, $this->pageMain());
            });
    }

    public function cbox($sc_id = 1){
        $nav_menu = $this->nav_menu;
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

            $hzhtml .= '<li class="list-group-item"><a '.$class.' href="/merchant/minapp?&hangzu_id='.$key.'">'.$items.'</a></li>';
        }
        //$hzhtml .= '<li class="list-group-item"><a href="/merchant/wxgzh?&sc_id='.$sc_id.'" target="_blank">水电费</a></li>';
        $hzhtml .= '</ul>';
        $box = new Box('操作项', $hzhtml);
        //$box->collapsable();
        return $box;
    }

    // 页面
    public function pageMain() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'minapp'])->first();
        $this->oauth = $oauth;
        $data = [];
        $tab  = Tab::make();
        $datas = Request()->all();
        $nav_menu = $this->nav_menu;
        $hangzu_id = !empty($datas['hangzu_id'])? $datas['hangzu_id']:'';
        if(!empty($oauth->id)){
            if($hangzu_id == 1 || $hangzu_id == ''){
                $tab->add('微信小程序[授权模式]', $this->tab1());
            }
            if($hangzu_id == 2){
                $tab->add($nav_menu[$hangzu_id], $this->tab2());
            }
            if($hangzu_id == 3){
                $tab->add($nav_menu[$hangzu_id], $this->tab3());
            }
            if($hangzu_id == 4){
                $tab->add($nav_menu[$hangzu_id], $this->tab4());
            }
            if($hangzu_id == 5){
                $tab->add($nav_menu[$hangzu_id], $this->tab5());
            }
            /*if($hangzu_id == 6){
                $tab->add('粉丝列表', $this->tab6());
            }*/
        }else{
            $tab->add('微信小程序[授权模式]', $this->tab1());
        }

        return $tab->withCard();
    }

    public function tab1() {

        $oauth        = $this->oauth;
        $openPlatform = app('wechat.open')->inits();
        if (empty($oauth->hotel_id)) {
            if (empty(Admin::user()->hotel_id)) {
                return $card = Card::make('授权异常', '请先完成酒店资料填写 >>> <a href="' . url('merchant/storeinfo') . '"> 前往填写 </a>');
            }
            $url   = $openPlatform->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?app_type=minapp&uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],2);
            $htmls = '<a target="_blank" href="' . $url . '"> >>> 去授权</a>  ';
            $htmls .= '<button type="button" class="btn btn-primary text-capitalize" onclick="window.location.reload()"><i class="feather icon-refresh-ccw"></i> 刷新 </button>';
            return $card = Card::make('扫码授权', $htmls);
        }

        $tips_html = '<span class="text-danger">已授权</span> 如果需要重新授权 >>> <a target="_blank" href="' . url('/merchant/afreshOauth') . '"> 点击</a> ';
        $alert     = Alert::make($tips_html, '说明')->info();
        $info = $this->viewWxMinappInfo();
        $card      = Card::make('授权信息', $alert.$info);
        return $card;
    }


    public function tab2() {
        $openPlatform = app('wechat.open')->inits();
        // 获取体验二维码
        $tpl_res  = $openPlatform->code_template->list();

        $tpl_list = []; //array_column($tpl_res['template_list'], 'template_id');
        foreach ($tpl_res['template_list'] as $key => $tpl) {
            $tpl_list[$tpl['template_id']] = $tpl['user_version'];
        }


        $grid = Grid::make(new WxopenMiniProgramVersion(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('template_id', '模板ID');
            $grid->column('user_version', '版本号');
            $grid->column('user_desc', '版本描述');
            $grid->column('auditid', '审核编号');
            $grid->column('audit_status', '审核状态')->using(WxopenMiniProgramVersion::Audit_status_arr)->label(WxopenMiniProgramVersion::Audit_status_label);
            $grid->column('fail_reason', '审核反馈')->display(function ($fail_reason) {
                if ($this->audit_status == 3) {
                    return '<span class="text-danger tips" data-title="' . $fail_reason . '"> 查看原因 </span>';
                } else {
                    return '';
                }

            });
            $grid->column('created_at', '提交时间');
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableBatchDelete();
            $grid->disableColumnSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                $rowdata = $actions->row->toArray();
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();

                // 提交小程序模板审核
                $modal = Modal::make()
                    ->lg()
                    ->title('查看体验版小程序')
                    ->body(ViewTiyanQrcodeForm::make()->payload($actions->row->toArray()))
                    ->button('<i class="feather icon-eye tips" data-title="查看体验版小程序"></i>');
                $actions->append($modal);


                if (!empty($rowdata['audit_status']) && $rowdata['audit_status'] == 4) {
                    $modal1 = Modal::make()
                        ->lg()
                        ->title('全网发布')
                        ->body(WxMinAppFabuForm::make()->payload($rowdata))
                        ->button('&nbsp;&nbsp;<i class="feather icon-navigation tips" data-title="全网发布"></i>');
                    $actions->append($modal1);
                }

                //$loginurl = URL::signedRoute('autologin', ['user' => Muser::where(['id'=>$actions->row->id])->first()],now()->addMinutes(1),true);
                //$actions->append('<span class="tips" data-title="查看体验版小程序" > <i class="feather icon-log-in"></i></span>');
            });
        });


        // 查看小程序信息
        //$grid->tools('<a class="btn btn-white btn-outline" target="_blank" href="' . admin_url('/viewMinappInfo') . '"><i class="feather icon-alert-circle"></i> 查看小程序信息</a>');

        // 设置小程序隐私信息
        $form = new WidgetsForm();
        $form->action('/yisiSettingSave');
        $form->confirm('确认现在提交吗？');
        $form->html('全部隐私信息相关配置已就绪');
        $form->disableResetButton();
        $modal = Modal::make()
            ->lg()
            ->title('提交小程序隐私信息')
            ->body($form)
            ->button('<button class="btn btn-white btn-outline"><i class="feather icon-arrow-up"></i> 提交小程序隐私信息</button>');

        $grid->tools($modal);

        // 提交小程序模板
        $form1 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
        $form1->action('/wxaCommit');
        $form1->confirm('确认现在提交吗？');
        $form1->select('template_id', '模板ID')->options($tpl_list)->required();
        $form1->text('user_version', '版本号')->required();
        $form1->text('user_desc', '描述')->required();
        $form1->disableResetButton();
        $modal = Modal::make()
            ->lg()
            ->title('提交小程序模板')
            ->body($form1)
            ->button('<button class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 提交小程序模板</button>');
        $grid->tools($modal);

        $card      = Card::make('发布小程序版本记录',$grid);
        return $card;
    }

    public function tab3(){
        $infos   = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        if ($infos) {
            //$form = new Form(new WxappConfig())->edit();
            $form = Form::make(new WxopenMiniProgramOauth())->edit($infos->id);
            $form->hidden('id')->value($infos->id);
            $form->action('/saveMinappPayconfig');
            $form->disableDeleteButton();
            $form->disableListButton();
            $form->disableViewButton();
            $form->disableResetButton();
            $form->disableViewButton();
            $form->disableEditingCheck();
            $form->disableHeader();
            $form->hidden('oauth_id')->value($infos->id);
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('AuthorizerAppid', '小程序AppId')->required();
            $form->text('appsecret', '小程序appSecret')->required();
            $form->text('sub_mch_id', '微信支付商户号')->required();
            $form->text('apikey', '微信支付Api密钥')->required();
            $form->textarea('cert_pem', '微信支付 apiclient_cert.pem');
            $form->textarea('key_pem', '微信支付apiclient_key.pem');
        } else {

            $form = $form = Form::make(new WxopenMiniProgramOauth());
            $form->html('<div style="font-size: 24px;color: #ddd;margin-top: 100px;text-align: center">请先完成小程序扫码授权</div>');
            $form->disableResetButton();
            $form->disableSubmitButton();
            //$form->action(url('/merchant/wxminapp'));
        }

        $card = Card::make('', $form);
        return $card;
    }

    // 成员管理
    public function tab4() {
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->tester->list();

        $grid = Grid::make(new WxopenMiniProgramVersion(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
            $grid->column('template_id', 'userstr');
            $grid->column('user_version', '版本号');
        });

        $card1 = Card::make('', $grid);
        return $card1;
    }

    public function tab5() {
        $form1 = new WidgetsForm();
        $form1->text('msgtpl1', '订房成功提醒')->help('类目: 服装/鞋/箱包');
        /*$form1->text('msgtpl2', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl3', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl4', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl5', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl6', '下单成功提醒(类目: 服装/鞋/箱包 )');*/
        $form1->disableResetButton();

        $card1 = Card::make('', $form1);
        return $card1;
    }


    // 页面
    public function pageMain01() {
        $data = [];
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('微信小程序[授权模式]', $this->tab2());
        $tab->add('微信小程序支付信息配置', $this->tab1());
        $tab->add('微信小程序 模板消息', $this->tab3());
        //$tab->add('轮播图', $this->tab3());
        //$tab->add('导航图标', $this->tab4());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
        return $tab->withCard();
    }

    public function tab1_old() {
        // 配置
        $user_id = Admin::user()->id;
        /*if(Admin::user()->hotel_id == 225){
           $res =  $this->getCategory();
           echo "<pre>";
           print_r($res);
           echo "</pre>";
           exit;

        }*/
        $infos   = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        if ($infos) {
            //$form = new Form(new WxappConfig())->edit();
            $form = Form::make(new WxopenMiniProgramOauth())->edit($infos->id);
            $form->hidden('id')->value($infos->id);
            $form->action('/saveMinappPayconfig');
            $form->disableDeleteButton();
            $form->disableListButton();
            $form->disableViewButton();
            $form->disableResetButton();
            $form->disableViewButton();
            $form->disableEditingCheck();
            $form->disableHeader();
            $form->hidden('oauth_id')->value($infos->id);
            $form->hidden('hotel_id')->value(Admin::user()->hotel_id);
            $form->text('AuthorizerAppid', '小程序AppId')->required();
            $form->text('appsecret', '小程序appSecret')->required();
            $form->text('sub_mch_id', '微信支付商户号')->required();
            $form->text('apikey', '微信支付Api密钥')->required();
            $form->textarea('cert_pem', '微信支付 apiclient_cert.pem');
            $form->textarea('key_pem', '微信支付apiclient_key.pem');
        } else {

            $form = $form = Form::make(new WxopenMiniProgramOauth());
            $form->html('<div style="font-size: 24px;color: #ddd;margin-top: 100px;text-align: center">请先完成小程序扫码授权</div>');
            $form->disableResetButton();
            $form->disableSubmitButton();
            //$form->action(url('/merchant/wxminapp'));
        }

        $card = Card::make('', $form);
        return $card;
    }

    public function tab2_old() {

        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'minapp'])->first();
        $openPlatform = app('wechat.open')->inits();
        if (empty($oauth->hotel_id)) {
            if (empty(Admin::user()->hotel_id)) {
                return $card = Card::make('授权异常', '请先完成酒店资料填写 >>> <a href="' . url('merchant/storeinfo') . '"> 前往填写 </a>');
            }
            $url   = $openPlatform->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?app_type=minapp&uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],2);
            $htmls = '<a target="_blank" href="' . $url . '"> >>> 去授权</a>  ';
            $htmls .= '<button type="button" class="btn btn-primary text-capitalize" onclick="window.location.reload()"><i class="feather icon-refresh-ccw"></i> 刷新 </button>';
            return $card = Card::make('扫码授权', $htmls);
        } else {
            // 获取体验二维码
            $tpl_res  = $openPlatform->code_template->list();

            $tpl_list = []; //array_column($tpl_res['template_list'], 'template_id');
            foreach ($tpl_res['template_list'] as $key => $tpl) {
                $tpl_list[$tpl['template_id']] = $tpl['user_version'];
            }


            $grid = Grid::make(new WxopenMiniProgramVersion(), function (Grid $grid) {
                $grid->model()->where(['hotel_id' => Admin::user()->hotel_id])->orderBy('id', 'DESC');
                $grid->column('template_id', '模板ID');
                $grid->column('user_version', '版本号');
                $grid->column('user_desc', '版本描述');
                $grid->column('auditid', '审核编号');
                $grid->column('audit_status', '审核状态')->using(WxopenMiniProgramVersion::Audit_status_arr)->label(WxopenMiniProgramVersion::Audit_status_label);
                $grid->column('fail_reason', '审核反馈')->display(function ($fail_reason) {
                    if ($this->audit_status == 3) {
                        return '<span class="text-danger tips" data-title="' . $fail_reason . '"> 查看原因 </span>';
                    } else {
                        return '';
                    }

                });
                $grid->column('created_at', '提交时间');
                $grid->disableCreateButton();
                $grid->disableDeleteButton();
                $grid->disableBatchDelete();
                $grid->disableColumnSelector();
                $grid->setActionClass(Grid\Displayers\Actions::class);
                $grid->actions(function ($actions) {
                    $rowdata = $actions->row->toArray();
                    // 去掉删除
                    $actions->disableDelete();
                    // 去掉编辑
                    $actions->disableEdit();
                    $actions->disableView();

                    // 提交小程序模板审核
                    $modal = Modal::make()
                        ->lg()
                        ->title('查看体验版小程序')
                        ->body(ViewTiyanQrcodeForm::make()->payload($actions->row->toArray()))
                        ->button('<i class="feather icon-eye tips" data-title="查看体验版小程序"></i>');
                    $actions->append($modal);


                    if (!empty($rowdata['audit_status']) && $rowdata['audit_status'] == 4) {
                        $modal1 = Modal::make()
                            ->lg()
                            ->title('全网发布')
                            ->body(WxMinAppFabuForm::make()->payload($rowdata))
                            ->button('&nbsp;&nbsp;<i class="feather icon-navigation tips" data-title="全网发布"></i>');
                        $actions->append($modal1);
                    }

                    //$loginurl = URL::signedRoute('autologin', ['user' => Muser::where(['id'=>$actions->row->id])->first()],now()->addMinutes(1),true);
                    //$actions->append('<span class="tips" data-title="查看体验版小程序" > <i class="feather icon-log-in"></i></span>');
                });
            });


            // 查看小程序信息
            $grid->tools('<a class="btn btn-white btn-outline" target="_blank" href="' . admin_url('/viewMinappInfo') . '"><i class="feather icon-alert-circle"></i> 查看小程序信息</a>');

            // 设置小程序隐私信息
            $form = new WidgetsForm();
            $form->action('/yisiSettingSave');
            $form->confirm('确认现在提交吗？');
            $form->html('全部隐私信息相关配置已就绪');
            $form->disableResetButton();
            $modal = Modal::make()
                ->lg()
                ->title('提交小程序隐私信息')
                ->body($form)
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-arrow-up"></i> 提交小程序隐私信息</button>');

            $grid->tools($modal);

            // 提交小程序模板
            $form1 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
            $form1->action('/wxaCommit');
            $form1->confirm('确认现在提交吗？');
            $form1->select('template_id', '模板ID')->options($tpl_list)->required();
            $form1->text('user_version', '版本号')->required();
            $form1->text('user_desc', '描述')->required();
            $form1->disableResetButton();
            $modal = Modal::make()
                ->lg()
                ->title('提交小程序模板')
                ->body($form1)
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 提交小程序模板</button>');
            $grid->tools($modal);

            //$grid->enableDialogCreate(); // 打开弹窗创建

            //$callout = Callout::make('内容', '标题')->light()->info()->removable();
            //$url          = app('wechat.open')->inits()->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],2,$oauth->AuthorizerAppid);

            $tips_html = '<span class="text-danger">已授权</span> 如果需要重新授权 >>> <a target="_blank" href="' . url('/merchant/afreshOauth') . '"> 点击</a> ';
            $alert     = Alert::make($tips_html, '说明')->info();

            $card      = Card::make('发布小程序版本记录', $alert . $grid);
            return $card;
        }

        // 代小程序实现业务
        //$miniProgram = $openPlatform->miniProgram(string $appId, string $refreshToken);
    }

    public function tab3_old() {
        // 模板消息
        $form1 = new WidgetsForm();
        $form1->text('msgtpl1', '订房成功提醒')->help('类目: 服装/鞋/箱包');
        /*$form1->text('msgtpl2', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl3', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl4', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl5', '下单成功提醒(类目: 服装/鞋/箱包 )');
        $form1->text('msgtpl6', '下单成功提醒(类目: 服装/鞋/箱包 )');*/
        $form1->disableResetButton();

        $card1 = Card::make('', $form1);
        return $card1;
    }

    // 获取业务域名校验文件
    public function getWebviewdomainConfirmfile() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        // 获取业务域名校验文件
        $res = $miniProgram->domain->getWebviewdomainConfirmfile();
        if (isset($res['errcode']) && $res['errcode'] == 0) {
            $file_path = public_path($res['file_name']);
            if (!file_exists($file_path)) {
                file_put_contents($file_path, $res['file_content']);
                $res['save_status'] = '已保存到本地';
            } else {
                $res['save_status'] = '文件已存在';
            }
        } else {
            $res['save_status'] = '未保存到本地';
        }
        return $res;
    }

    public function setCategory(){
        //$oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);

        // 酒店用
        $mdata = [
            [
                'first'=> '231', //旅游服务
                'second'=> '235',//住宿服务
            ]
        ];
        // 测试用
        /*$mdata = [
            [
                'first'=> '304',//商家自营
                'second'=> '317',// 运动户外
            ]
        ];*/
        $res     = $miniProgram->setting->addCategories($mdata);
        addlogs('setCategory',$mdata,$res);
        return $res;
    }

    // 获取已设置的所有类目
    public function getAllCategories(){
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res     = $miniProgram->setting->getAllCategories();
        addlogs('getAllCategories',[],$res);
        return $res;
    }


    // 配置小程序服务器域名
    public function modify_domain() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        // 去掉https:// 得到域名
        $no_host = str_replace('https://', '', env('APP_URL'));
        $mdata   = [
            "action"          => "add",
            "requestdomain"   => [env('APP_URL')],
            "wsrequestdomain" => ["wss://" . $no_host],
            "uploaddomain"    => [env('APP_URL')],
            "downloaddomain"  => [env('APP_URL')],
            "udpdomain"       => ["udp://" . $no_host],
            "tcpdomain"       => ["tcp://" . $no_host]
        ];
        $res     = $miniProgram->domain->modify($mdata);
        addlogs('modify',$mdata,$res);

        $res1 = $this->getWebviewdomainConfirmfile();
        addlogs('getWebviewdomainConfirmfile',[],$res1);
        // 设置业务域名
        $WebviewDomain = [env('APP_URL')];
        $res2          = $miniProgram->domain->setWebviewDomain($WebviewDomain);
        addlogs('setWebviewDomain',$WebviewDomain,$res2);
        //$res2 = $miniProgram->domain->webviewDomain(['action'=> 'get']);
        return [
            '配置小程序服务器域名' => $res,
            '获取业务域名校验文件' => $res1,
            '设置业务域名'     => $res2
        ];
    }

    // 发布小程序
    public function wxaCommit(Request $request) {
        $validator = \Validator::make($request->all(), [
            'template_id'  => 'required',
            'user_version' => 'required',
            'user_desc'    => 'required',
            //'ext_json' => 'required',
        ], [
            'template_id.required'  => '小程序模板ID 不能为空',
            'user_version.required' => '版本号 不能为空',
            'user_desc.required'    => '版本描述不能 不能为空',
            'ext_json.required'     => 'ext_json 不能为空',

        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $user_version = $request->user_version;
        $template_id  = $request->template_id;
        //$ext_json      = $request->ext_json;
        $user_desc    = $request->user_desc;
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $hotel_name   = Hotel::where(['id' => Admin::user()->hotel_id])->value('name');
        $ext_json_arr = [
            'extEnable'    => false,
            'extAppid'     => $oauth->AuthorizerAppid,
            'directCommit' => false,
            'ext'          => [
                "app_name" => $hotel_name,
                "app_id"   => $oauth->AuthorizerAppid,
                "hotel_id" => $oauth->hotel_id,
                "webhost"  => env('APP_URL'),
                'attr'     => [
                    'host' => env('APP_URL')
                ]
            ],
            'extPages'     => [],
        ];
        $ext_json     = json_encode($ext_json_arr, JSON_UNESCAPED_UNICODE);
        $openPlatform = app('wechat.open');

        // 提交小程序隐私信息
        $counts = WxopenMiniProgramVersion::where(['minapp_id'=>$oauth->AuthorizerAppid])->count();
        if(!$counts){
            $res0 = $this->setCategory();//设置小程序类目
            $res1 = $openPlatform->tijiaoYinsi(Admin::user()->hotel_id); //设置隐私信息
            $res2 = $this->modify_domain();//设置业务域名

        }
        // 保存记录
        WxopenMiniProgramVersion::create([
            'user_id'      => Admin::user()->id,
            'hotel_id'     => Admin::user()->hotel_id,
            'minapp_id'    => $oauth->AuthorizerAppid,
            'ToUserName'   => $oauth->ToUserName,
            'template_id'  => $template_id,
            'user_version' => $user_version,
            'user_desc'    => $user_desc,
            'ext_json'     => $ext_json
        ]);
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->code->commit($template_id, $ext_json, $user_version, $user_desc);
        addlogs('minapp_wxCommit',[$template_id, $ext_json, $user_version, $user_desc],$res);
        return JsonResponse::make()->data($res)->success('已提交')->refresh();
    }

    // 重新授权
    public function afreshOauth(Request $request) {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=>'minapp'])->first();
        $auth_url          = app('wechat.open')->inits()->getPreAuthorizationUrl(env('APP_URL') . '/hotel/notify/oauthNotify?uid=' . Admin::user()->id . '&hid=' . Admin::user()->hotel_id,[],2,$oauth->AuthorizerAppid);
        return redirect($auth_url);
    }

    public function viewWxMinappInfo(){
        $miniProgram = app('wechat.open')->hotelMiniProgram(Admin::user()->hotel_id);
        $minappinfo  = $miniProgram->account->getBasicInfo();
        $categories = $miniProgram->setting->getCategories();
        $form        = new WidgetsForm();
        $form->disableSubmitButton();
        $form->disableResetButton();
        if (!empty($minappinfo['errmsg']) && $minappinfo['errmsg'] == 'ok') {
            //$form->html('查看隐私保存信息配置<a target="_blank" href="'.admin_url('yisiSettingView').'"> 查看</a>');
            $minapp_qrcode = app('wechat.open')->getMinappQrcode('',Admin::user()->hotel_id,'pages/index/index',Admin::user()->hotel_id.'-qrcode.png');
            if($minapp_qrcode !== false){
                $form->html('<div style="margin-top: 0px;"><img width="140" src="'.$minapp_qrcode.'" /></div>')->label('小程序二维码');
            }
            $form->text('appid', '小程序appid')->default($minappinfo['appid']);
            $form->text('nickname', '小程序名称')->default($minappinfo['nickname']);
            $form->html('<img src="'.$minappinfo['head_image_info']['head_image_url'].'" width="120">')->label('小程序图标');
            $form->text('signature', '小程序简介')->default($minappinfo['signature_info']['signature']);
            $form->text('principal_name', '主体公司')->default($minappinfo['principal_name']);
            $form->radio('realname_status', '实名验证状态')->options(['1' => '已验证', '0' => '未验证'])->default($minappinfo['realname_status']);
            $form->html('<h3>微信认证信息</h3>');
            $form->radio('naming_verify	', '名称认证')->options(['1' => '已认证', '' => '未认证'])->default($minappinfo['wx_verify_info']['naming_verify']);
            $annual_review = !empty($minappinfo['wx_verify_info']['annual_review']) ? $minappinfo['wx_verify_info']['annual_review']:'';
            $annual_review_begin_time = !empty($minappinfo['wx_verify_info']['annual_review_begin_time']) ? $minappinfo['wx_verify_info']['annual_review_begin_time']:'';
            $annual_review_end_time = !empty($minappinfo['wx_verify_info']['annual_review_end_time']) ? $minappinfo['wx_verify_info']['annual_review_end_time']:'';
            $customer_type = isset($minappinfo['customer_type']) ? $minappinfo['customer_type']:'';
            $customer_type_arr = [
                '' => '未完成认证',
                '0' => '未完成认证',
                '1' => '企业',
                '2' => '企业媒体',
                '12' => '个体工商户',
                '14' => '海外企业',
                '15' => '个人',
            ];
            $form->select('customer_type', '微信认证')->options($customer_type_arr)->default($customer_type);
            $form->radio('annual_review	', '是否需要年审')->options(['1' => '需要', '' => '不需要'])->default($annual_review);
            $form->text('annual_review_begin_time	', '年审开始时间')->default(date('Y-m-d H:i:s',$annual_review_begin_time));
            $form->text('annual_review_end_time	', '年审截止时间')->default(date('Y-m-d H:i:s',$annual_review_end_time));
            if(empty($minappinfo['wx_verify_info']['naming_verify'])){

            }
            if(!empty($categories['categories'])){
                $audit_status_arr = [
                    '1' => '审核中',
                    '2' => '审核不通过',
                    '3'=> '审核通过',
                ];
                // 1 审核中 2 审核不通过 3 审核通过
                $htmls = '';
                foreach ($categories['categories'] as $key => $items) {
                    $audit_reason = !empty($items['audit_reason']) ? ": ".$items['audit_reason']:'';
                    $audit_status = !empty($audit_status_arr[$items['audit_status']]) ? $audit_status_arr[$items['audit_status']]:'';
                    $htmls .= ($key+1).'类目:'.$items['first_name'].'->'.$items['second_name'].' 审核状态:'.$audit_status .$audit_reason.' <br/>';
                }

                $form->html($htmls)->label('小程序类目');
            }

        } else {
            $form->html('<h3>未获取小程序信息</h3>');
        }
        $card = Card::make('', $form);
        return $card;
    }

    // 查看小程序信息
    public function viewMinappInfo(Content $content) {
        $miniProgram = app('wechat.open')->hotelMiniProgram(Admin::user()->hotel_id);
        $minappinfo  = $miniProgram->account->getBasicInfo();
        $categories = $miniProgram->setting->getCategories();
        $form        = new WidgetsForm();
        $form->disableSubmitButton();
        $form->disableResetButton();
        if (!empty($minappinfo['errmsg']) && $minappinfo['errmsg'] == 'ok') {
            $form->html('查看隐私保存信息配置<a target="_blank" href="'.admin_url('yisiSettingView').'"> 查看</a>');
            $form->text('appid', '小程序appid')->default($minappinfo['appid']);
            $form->text('nickname', '小程序名称')->default($minappinfo['nickname']);
            $form->html('<img src="'.$minappinfo['head_image_info']['head_image_url'].'" width="120">')->label('小程序图标');
            $form->text('signature', '小程序简介')->default($minappinfo['signature_info']['signature']);
            $form->text('principal_name', '主体公司')->default($minappinfo['principal_name']);
            $form->radio('realname_status', '实名验证状态')->options(['1' => '已验证', '0' => '未验证'])->default($minappinfo['realname_status']);
            $form->html('<h3>微信认证信息</h3>');
            $form->radio('naming_verify	', '名称认证')->options(['1' => '已认证', '' => '未认证'])->default($minappinfo['wx_verify_info']['naming_verify']);
            $annual_review = !empty($minappinfo['wx_verify_info']['annual_review']) ? $minappinfo['wx_verify_info']['annual_review']:'';
            $annual_review_begin_time = !empty($minappinfo['wx_verify_info']['annual_review_begin_time']) ? $minappinfo['wx_verify_info']['annual_review_begin_time']:'';
            $annual_review_end_time = !empty($minappinfo['wx_verify_info']['annual_review_end_time']) ? $minappinfo['wx_verify_info']['annual_review_end_time']:'';
            $customer_type = isset($minappinfo['customer_type']) ? $minappinfo['customer_type']:'';
            $customer_type_arr = [
                '' => '未完成认证',
                '0' => '未完成认证',
                '1' => '企业',
                '2' => '企业媒体',
                '12' => '个体工商户',
                '14' => '海外企业',
                '15' => '个人',
            ];
            $form->select('customer_type', '微信认证')->options($customer_type_arr)->default($customer_type);
            $form->radio('annual_review	', '是否需要年审')->options(['1' => '需要', '' => '不需要'])->default($annual_review);
            $form->text('annual_review_begin_time	', '年审开始时间')->default(date('Y-m-d H:i:s',$annual_review_begin_time));
            $form->text('annual_review_end_time	', '年审截止时间')->default(date('Y-m-d H:i:s',$annual_review_end_time));
            if(empty($minappinfo['wx_verify_info']['naming_verify'])){

            }
            if(!empty($categories['categories'])){
                $audit_status_arr = [
                    '1' => '审核中',
                    '2' => '审核不通过',
                    '3'=> '审核通过',
                ];
                // 1 审核中 2 审核不通过 3 审核通过
                $htmls = '';
                foreach ($categories['categories'] as $key => $items) {
                    $audit_reason = !empty($items['audit_reason']) ? ": ".$items['audit_reason']:'';
                    $audit_status = !empty($audit_status_arr[$items['audit_status']]) ? $audit_status_arr[$items['audit_status']]:'';
                    $htmls .= ($key+1).'类目:'.$items['first_name'].'->'.$items['second_name'].' 审核状态:'.$audit_status .$audit_reason.' <br/>';
                }

                $form->html($htmls)->label('小程序类目');
            }

        } else {
            $form->html('<h3>未获取小程序信息</h3>');
        }
        $card = Card::make('', $form);
        return $content
            ->header('小程序信息')
            ->description('基础信息')
            ->breadcrumb(['text' => '小程序信息', 'uri' => ''])
            ->body($card);
    }

    // 提交小程序隐私信息
    public function yisiSettingSave(Request $request) {
        $res = app('wechat.open')->tijiaoYinsi(Admin::user()->hotel_id);
        if(!empty($res['errmsg']) && $res['errmsg'] == 'ok'){
            return JsonResponse::make()->data($res)->success('成功！')->refresh();
        }
        return JsonResponse::make()->data($res)->error('提交失败');
    }

    // 查看小程序隐私信息
    public function yisiSettingView(Content $content){
        $miniProgram = app('wechat.open')->hotelMiniProgram(Admin::user()->hotel_id);
        $data['data'] = $miniProgram->setting->getPrivacysetting();
        return $content->body(admin_view('merchant.commonPage',$data));
    }

    // 保存小程序支付参数
    public function saveMinappPayconfig(Request $request){
        $validator = \Validator::make($request->all(), [
            'oauth_id' => 'required',
            'appsecret'  => 'required',
            'sub_mch_id' => 'required',
            'apikey'    => 'required',
            //'cert_pem' => 'required',
        ], [
            'oauth_id.required'  => '微信授权ID 不能为空',
            'appsecret.required'  => '小程序appSecret 不能为空',
            'sub_mch_id.required' => '微信支付商户号 不能为空',
            'apikey.required'    => '微信支付Api密钥 不能为空',
            'cert_pem.required'     => '支付证书 cert_pem 不能为空',
            'key_pem.required'  => '支付证书 key_pem 不能为空',

        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $hotel_id = Admin::user()->hotel_id;
        $oauth_id = $request->get('oauth_id');
        $where = [
            'id'=> $oauth_id,
            'hotel_id' => $hotel_id,
        ];
        $info = WxopenMiniProgramOauth::where($where)->first();
        if(!$info){
            return (new WidgetsForm())->response()->error('未找到小程序授权信息');
        }
        $updata = [
            'appsecret' => $request->get('appsecret'),
            'sub_mch_id' => $request->get('sub_mch_id'),
            'apikey'=> $request->get('apikey'),
            'cert_pem'=> $request->get('cert_pem',''),
            'key_pem'=> $request->get('key_pem',''),
        ];
        WxopenMiniProgramOauth::where($where)->update($updata);

        // 添加服务商到分账接受人
        \App\Models\Hotel\ProfitsharingReceiver::addIsvReceiverToPay($hotel_id);
        return (new WidgetsForm())->response()->success('保存成功')->refresh();
    }
}
