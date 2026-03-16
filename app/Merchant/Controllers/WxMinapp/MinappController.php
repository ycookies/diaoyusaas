<?php

namespace App\Merchant\Controllers\WxMinapp;

use App\Http\Controllers\Controller;
use App\Merchant\Actions\Form\ViewTiyanQrcodeForm;
use App\Merchant\Actions\Form\WxMinAppFabuForm;
use App\Merchant\Actions\Form\WxMinAppGuanzhuGzh;
use App\Models\Hotel\Hotel;
use App\Models\Hotel\WxappConfig;
use App\Models\Hotel\HotelSetting;
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
use App\Models\Hotel\MiniprogramPage;
use App\Merchant\Renderable\MinappPageQrcode;
use App\Merchant\Renderable\MiniprogramPageEditForm;
use App\Merchant\Repositories\MiniJumpQRCode;
use App\Merchant\Actions\Grid\QrcodeJumpPublish;
use App\Merchant\Actions\Grid\DeleteJumpQRCode;
use App\Merchant\Actions\Form\ActionJumpQRCode;

// 小程序
class MinappController extends Controller {

    public $oauth;
    public $nav_menu = [
        '1' => '基本信息',
        '2' => '版本管理',
        '3' => '支付配置',
        '4' => '页面管理',
        '5' => '模板消息',
        '6' => '小程序二维码',
    ];

    public function index(Content $content) {
        // 解决row col 间距
        Admin::style(<<<CSS
    .no-gutters>.col, .no-gutters>[class*=col-] {
    padding-left: 5px !important;
}
CSS            );
        return $content
            ->header('小程序管理')
            ->description('微信小程序')
            ->breadcrumb(['text' => '小程序管理', 'uri' => ''])
            ->row(function(Row $row) {
                $row->noGutters();// 无间距
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

            $hzhtml .= '<li class="list-group-item"><a '.$class.' href="/merchant/minapp?hangzu_id='.$key.'">'.$items.'</a></li>';
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
                return $this->pageMain_old();
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
            if($hangzu_id == 6){
                $tab->add($nav_menu[$hangzu_id], $this->tab6());
            }
        }else{
            $tab->add('微信小程序[授权模式]', $this->tab1());
        }

        return $tab->withCard();
    }

    // 页面
    public function pageMain_old() {
        $data = [];
        $tab  = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('小程序模板-版本管理', $this->tab2());
        //$tab->add('微信小程序支付信息配置', $this->tab3());
        //$tab->add('微信小程序 模板消息', $this->tab3());
        //$tab->add('轮播图', $this->tab3());
        //$tab->add('导航图标', $this->tab4());
        // 添加选项卡链接
        //$tab->addLink('跳转链接', 'http://xxx');
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
                $tpl_list[$tpl['template_id']] = $tpl['user_version'].'(ID:'.$tpl['template_id'].')';
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
                        ->title('查看体验版小程序/提交审核')
                        ->body(ViewTiyanQrcodeForm::make()->payload($actions->row->toArray()))
                        ->button('<i class="feather icon-eye tips" data-title="查看体验版小程序/提交审核"></i>');
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

            // 设置小程序体验人员
            $form1 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
            $form1->action('/wxaBindTester');
            $form1->confirm('确认现在提交吗？');
            $form1->text('wechatid', '微信号')->help('非手机号码')->required();
            $form1->radio('type', '操作类型')->options(['bind'=> '绑定','unbind'=>'解绑'])->required();
            $form1->disableResetButton();
            $modal = Modal::make()
                ->lg()
                ->title('小程序体验人员')
                ->body($form1)
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-plus-circle"></i> 体验人员</button>');
            $grid->tools($modal);

            $form09 = new WidgetsForm();//Form::make(new WxopenMiniProgramOauth());
            $form09->action('/changeVisitstatus');
            $form09->confirm('确认现在提交吗？');
            //$form09->text('wechatid', '微信号')->help('非手机号码')->required();

            $form09->radio('type', '操作类型')
                ->options(['close'=> '暂停服务','open'=>'开启服务'])
                ->help('')
                ->required();
            $form09->disableResetButton();
            $modal09 = Modal::make()
                ->lg()
                ->title('服务状态')
                ->body($form09)
                ->button('<button class="btn btn-white btn-outline">设置服务状态</button>');
            $grid->tools($modal09);

            // 设置扫码关注的公众号
            $modal = Modal::make()
                ->lg()
                ->title('设置关注的公众号')
                ->body(WxMinAppGuanzhuGzh::make()->payload(['hote_id'=> Admin::user()->hotel_id]))
                ->button('<button class="btn btn-white btn-outline"><i class="feather icon-settings"></i> 设置关注的公众号</button>');
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

    public function tab3() {
        // 配置
        $user_id = Admin::user()->id;
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
            //$form->text('appsecret', '小程序appSecret')->required();
            $form->text('sub_mch_id', '微信支付商户号')->required();
            /*$form->text('apikey', '微信支付Api密钥')->required();
            $form->textarea('cert_pem', '微信支付 apiclient_cert.pem');
            $form->textarea('key_pem', '微信支付apiclient_key.pem');*/
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

    // 小程序页面管理
    public function tab4() {
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);

        //$pages_list = $miniProgram->code->getPage();
        //addlogs('miniProgram_code_getPage',['hotel_id'=> Admin::user()->hotel_id],$pages_list);



        $grid = Grid::make(new MiniprogramPage(), function (Grid $grid) {
            $grid->model()->where(['hotel_id' => Admin::user()->hotel_id,'miniapp'=>'wx'])->orderBy('id', 'DESC');
            $grid->column('type','所属分类')->using(MiniprogramPage::Type_arr);
            $grid->column('name','名称');
            //$grid->column('open_type');
            //$grid->column('icon','图标');
            $grid->column('path','路径');
            $grid->column('qrcode','二维码')->modal('查看',function ($modal) {
                // 设置弹窗标题
                $modal->title('页面路径二维码');
                // 自定义图标
                $modal->icon('fa fa-qrcode');
                //$modal->body('这是二维码'.$this->name . '-'.$this->path);
                //$card = new Card(null, '这是二维码'.$this->name . '-'.$this->path);
                return MinappPageQrcode::make()->payload(['hotel_id'=> $this->hotel_id,'name'=>$this->name,'path'=> $this->path]);
            });;
            $grid->column('status','状态')->bool();

            $modal1 = Modal::make()
                ->lg()
                ->title('新增页面')
                ->body(\App\Merchant\Renderable\MiniprogramPageAddForm::make())
                ->button('<button class="btn btn-primary"><i class="feather icon-plus"></i> 新增页面</span></button>');
            $grid->tools($modal1);

            $grid->quickSearch(['name', 'path'])->placeholder('页面名称,路径');
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();

                $modal = Modal::make()
                    ->lg()
                    ->title('修改页面信息')
                    ->body(MiniprogramPageEditForm::make()->payload($actions->row->toArray()))
                    ->button('<i class="feather icon-edit-1 grid-action-icon tips" data-title="修改"></i>');
                $actions->append($modal);
            });
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->addTableClass(['table-text-center']);
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('type', '所属分类', MiniprogramPage::Type_arr);
            });
        });

        $card1 = Card::make('', $grid);
        return $card1;
    }

    // 添加订阅消息
    // ID:235  住宿服务
    // 2099  酒店预订成功通知
    // 2100  酒店预订取消通知
    // 33857 预订失败通知
    // u9WJK_T-Z3elqPKEI_3Wp_QXtzC6JWXSivGGvGw1qV0
    public function tab5() {
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);

        $request =  Request();
        $field = [
            'booking_minapp_msg_tpl_success',
            'booking_minapp_msg_tpl_cancel',
            'booking_minapp_msg_tpl_fail',
        ];
        $formdata = HotelSetting::getlists($field,Admin::user()->hotel_id);


        //$tid  = '33857';
        //$res = $miniProgram->subscribe_message->getTemplateKeywords($tid);

        $booking_minapp_msg_tpl = [
            'booking_minapp_msg_tpl_success' => '',
            'booking_minapp_msg_tpl_cancel' => '',
            'booking_minapp_msg_tpl_fail' => '',
        ];
        if(empty($formdata['booking_minapp_msg_tpl_success'])){
            $tid = 2099;
            $kidList = [1,6,2,3,4];
            $sceneDesc = '酒店预订成功通知';
            $res = $miniProgram->subscribe_message->addTemplate($tid, $kidList, $sceneDesc);
            if(!empty($res['priTmplId'])){
                $booking_minapp_msg_tpl['booking_minapp_msg_tpl_success'] = $res['priTmplId'];
            }

            $tid = 2100;
            $kidList = [1,8,4,6,7];
            $sceneDesc = '酒店预订取消通知';
            $res = $miniProgram->subscribe_message->addTemplate($tid, $kidList, $sceneDesc);
            if(!empty($res['priTmplId'])){
                $booking_minapp_msg_tpl['booking_minapp_msg_tpl_cancel'] = $res['priTmplId'];
            }

            $tid = 33857;
            $kidList = [1,6,4,5];
            $sceneDesc = '预订失败通知';
            $res = $miniProgram->subscribe_message->addTemplate($tid, $kidList, $sceneDesc);
            if(!empty($res['priTmplId'])){
                $booking_minapp_msg_tpl['booking_minapp_msg_tpl_fail'] = $res['priTmplId'];
            }
            $status = HotelSetting::createRow(array_filter($booking_minapp_msg_tpl),Admin::user()->hotel_id,'booking_minapp_msg_tpl');
        }

        // 模板消息
        $field = [
            'booking_minapp_msg_tpl_success',
            'booking_minapp_msg_tpl_cancel',
            'booking_minapp_msg_tpl_fail',
        ];
        $formdata = HotelSetting::getlists($field,Admin::user()->hotel_id);
        $form1 = new WidgetsForm($formdata);
        $form1->width(8,4);
        $form1->text('booking_minapp_msg_tpl_success', '酒店预订成功通知 模板ID:<br/> booking_minapp_msg_tpl_success')->help('类目: 住宿服务');
        $form1->text('booking_minapp_msg_tpl_cancel', '酒店预订取消通知 模板ID:<br/> booking_minapp_msg_tpl_cancel')->help('类目: 住宿服务');
        $form1->text('booking_minapp_msg_tpl_fail', '预订失败通知 模板ID:<br/> booking_minapp_msg_tpl_fail')->help('类目: 住宿服务');

        $form1->disableResetButton();
        $form1->disableSubmitButton();
        $tips_html = "<ul><li>以下订阅模板消息的设置来自于：类目（住宿服务）</li></ul>";
        $alert     = Alert::make($tips_html, '说明')->info();

        $card1 = Card::make('', $alert.$form1);
        return $card1;
    }

    // 小程序二维码
    public function tab6() {
        $grid = Grid::make(new MiniJumpQRCode(), function (Grid $grid) {
            $grid->column('prefix','二维码规则');
            $grid->column('path','小程序功能页面');
            $grid->column('state','发布状态')->using(['1'=>'未发布','2'=> '已发布']);

            $modal = Modal::make()
                ->lg()
                ->title('新增二维码规则')
                ->body(ActionJumpQRCode::make())
                ->button('<button class="btn btn-white btn-outline"> <i class="feather icon-plus"></i> 新增规则</button>');

            $grid->tools($modal);
            $grid->disableRowSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                //if($actions->row->state == 1){
                    $actions->append(QrcodeJumpPublish::make());
                //}
                $actions->append(DeleteJumpQRCode::make());

                $modal = Modal::make()
                    ->lg()
                    ->title('修改二维码规则')
                    ->body(ActionJumpQRCode::make()->payload($actions->row->toArray()))
                    ->button('&nbsp; 修改');
                $actions->append($modal);

            });

        });
        $alert = Alert::make('扫普通链接二维码打开小程序 规则管理 <a href="https://developers.weixin.qq.com/miniprogram/introduction/qrcode.html" target="_blank">查看官方文档</a>','提示')->info();
        $card1 = Card::make('', $alert.$grid);
        return $card1;
    }

    // 获取订阅消息模板分类
    public function tab7() {
        // 模板消息
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->subscribe_message->getCategory();
        echo "<pre>";
        print_r($res);
        echo "</pre>";
        exit;
        $form1 = new WidgetsForm();
        $form1->text('msgtpl1', '订房成功提醒')->help('类目: 服装/鞋/箱包');

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

    // 获取小程序服务器域名
    public function getModifyDomainList() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res     = $miniProgram->domain->getEffectiveDomain();
        return $res;
    }

    // 获取生效后的业务域名
    public function getEffectiveWebviewdomain() {
        $oauth        = WxopenMiniProgramOauth::where(['hotel_id' => Admin::user()->hotel_id,'app_type'=> 'minapp'])->first();
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res     = $miniProgram->domain->getEffectiveWebviewdomain();
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
            'extAppid'     => 'wx7246aea8d02dabdf',
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
        info('提交的ext',$ext_json_arr);
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

    // 操作体验者
    public function wxaBindTester(Request $request){
        $validator = \Validator::make($request->all(), [
            'wechatid'  => 'required',
            'type'=> 'required',
        ], [
            'wechatid.required'  => '微信号 不能为空',
            'type.required'  => '操作类型 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $wechatid = $request->get('wechatid');
        $type = $request->get('type');
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        if($type == 'bind'){
            $res = $miniProgram->tester->bind($wechatid);
        }else{
            $res = $miniProgram->tester->unbind($wechatid);
        }

        addlogs('wxaBindTester',['wechatid'=> $wechatid,'type'=> $type],$res);
        if(isset($res['errcode']) && $res['errcode'] == 0){
            return JsonResponse::make()->data($res)->success('已提交成功')->refresh();
        }
        $emsg = '提交失败，请稍候再试';
        if(isset($res['errcode'])){
            $errcode_arr = [
                '85001' => '微信号不存在或微信号设置为不可搜索',
                '85002' => '小程序绑定的体验者数量达到上限',
                '85003' => '微信号绑定的小程序体验者达到上限',
                '85004' => '微信号已经绑定',
            ];
            if(!empty($errcode_arr[$res['errcode']])){
                $emsg = $errcode_arr[$res['errcode']];
            }
        }
        return JsonResponse::make()->error($emsg);
    }

    // 设置扫码关注的公众号
    public function wxaUpdateshowwxaitem(Request $request){
        $validator = \Validator::make($request->all(), [
            'wxa_subscribe_biz_flag'  => 'required',
            //'type'=> 'required',
        ], [
            'wxa_subscribe_biz_flag.required'  => '请选择 不能为空',
            //'type.required'  => '操作类型 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $wxa_subscribe_biz_flag = $request->get('wxa_subscribe_biz_flag');

        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $oauthinfo = $openPlatform->getOauthInfo('',Admin::user()->hotel_id,'','wxgzh');
        if(empty($oauthinfo->AuthorizerAppid)){
            return JsonResponse::make()->error('你还未操作公众号授权');
        }
        $app_id = $oauthinfo->AuthorizerAppid;
        $res = $miniProgram->setting->setDisplayedOfficialAccount($app_id);

        addlogs('wxaUpdateshowwxaitem',['wxa_subscribe_biz_flag'=> $wxa_subscribe_biz_flag,'app_id'=> $app_id],$res);
        if(isset($res['errcode']) && $res['errcode'] == 0){
            return JsonResponse::make()->data($res)->success('已提交成功')->refresh();
        }
        $emsg = '提交失败，请稍候再试';
        return JsonResponse::make()->error($emsg);
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
            $minapp_qrcode = app('wechat.open')->getMinappQrcode('',Admin::user()->hotel_id,'pages/index/index',Admin::user()->hotel_id.'-qrcode.png',0);
            //$minapp_qrcode = app('wechat.open')->getUnlimitedQRCode('',Admin::user()->hotel_id,'',['page'=> 'pages/index/index'],Admin::user()->hotel_id.'-qrcode.png');
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
            //'appsecret'  => 'required',
            'sub_mch_id' => 'required',
            //'apikey'    => 'required',
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

    // 设置小程序的服务状态
    public function changeVisitstatus(Request $request){
        $validator = \Validator::make($request->all(), [
            'type'=> 'required',
        ], [
            'type.required'  => '操作类型 不能为空',
        ]);
        if ($validator->fails()) {
            return (new WidgetsForm())->response()->error($validator->errors()->first());
        }
        $type = $request->get('type');
        $openPlatform = app('wechat.open');
        $miniProgram = $openPlatform->hotelMiniProgram(Admin::user()->hotel_id);
        $res = $miniProgram->code->changeVisitStatus($type);
        if(!empty($res['errmsg']) && $res['errmsg'] == 'ok'){
            return (new WidgetsForm())->response()->success('设置成功')->refresh();
        }

        $errmsg = !empty($res['errmsg']) ? $res['errmsg']:'';
        return (new WidgetsForm())->response()->error('设置失败:'.$errmsg);

    }
}
