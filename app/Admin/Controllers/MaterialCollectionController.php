<?php

namespace App\Admin\Controllers;

use App\Models\Hotel\Hotel;
use App\Models\Hotel\JinjianDoc;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Admin\Extensions\Form\ExampleImg;
use App\Admin\Extensions\Form\MultipleExampleImg;
/*use SuperEggs\DcatDistpicker\Filter\DistpickerFilter;
use SuperEggs\DcatDistpicker\Form\Distpicker;
use SuperEggs\DcatDistpicker\Grid\Distpicker as GridDistpicker;*/
//use App\Models\Hotel\Hotel;
// 材料收集
class MaterialCollectionController extends Controller {
    public $doc_info; // 进件资料对象

    // 创建商户进件链接
    public function createLink(Content $content) {
        Admin::css('/css/material-collection.css');
        Admin::script(
            <<<JS
(function () {
   var clipboard = new ClipboardJS('.clipboard-txt');
   clipboard.on('success', function(e) {
       e.clearSelection();
       layer.msg('已复制');
    });
    clipboard.on('error', function(e) {
        e.clearSelection();
        layer.msg('复制内容失败');
    });
})()    
JS
        );
        admin_inject_section(Admin::SECTION['APP_INNER_BEFORE'], function () {
            $html = <<<HTML
<div class="header" style="height: 100px;margin-bottom: 20px;">
        <div class="wx-wrap">
            <div class="logo">
                <h1 class="main-logo"><a href="#">微信支付商户平台</a></h1>
                <div class="sub-logo"></div>
            </div>
        </div>
    </div>
HTML;
            return $html;
        });


        $row = function (Row $row) {
            $form = Form::make(new JinjianDoc());
            $form->confirm('确认现在生成吗？');
            $form->action('/material-collect/create-link/save');
            $form->block(12, function (Form\BlockForm $form) {

                $form->display('hotel_bd', '业务BD')->value('钟总');

                $form->next(function (Form\BlockForm $form) {
                    $business_code = '1566291601_' . time();
                    $form->title('创建商户进件链接<br/><span style="font-size: 12px;font-weight: 400;color:#737373">每个链接唯一使用</span>');
                    $form->text('hotel_name', '酒店名称')->required();
                    $form->hidden('doc_link', '酒店名称')->value(url('admin/material-collect/form?business_code=' . $business_code))->required();
                    $form->hidden('business_code', '酒店名称')->value($business_code)->required();
                    $form->hidden('hotel_bd', '业务BD')->value('钟总');
                    //$form->button('保存并下一步');
                    $form->submitButton('创建进件链接');
                });
                $form->next(function (Form\BlockForm $form) {
                    $form->title('商户进件链接管理<br/><span style="font-size: 12px;font-weight: 400;color:#737373">所有历史进件链接</span>');
                    $grid = Grid::make(new JinjianDoc(), function (Grid $grid) {
                        $grid->model()->orderBy('id', 'DESC');
                        $grid->column('id');
                        $grid->column('hotel_name', '酒店名');
                        $grid->column('doc_link', '资料进件链接')->display(function ($doc_link) {
                            return "<a target='_blank' href='" . $doc_link . "'>查看</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class='clipboard-txt' data-clipboard-text='" . $doc_link . "'> <i class='feather icon-copy'></i></span>";
                        });
                        $grid->actions(function ($actions) {
                            $id = $actions->getKey();
                            // 去掉删除
                            $actions->disableDelete();
                            // 去掉编辑
                            $actions->disableEdit();
                        });
                        $grid->disableActions();
                        $grid->disableBatchActions();
                        $grid->disableDeleteButton();
                        $grid->disableBatchDelete();
                        $grid->disableCreateButton();
                        $grid->setActionClass(Grid\Displayers\Actions::class);
                    });
                    $form->html($grid);
                });
            });
            $row->column(1, '');
            $row->column(10, $form);
            $row->column(1, '');
        };

        return $content->full()->header('商户进件')->description('支付商户进件')->row($row);
    }

    //
    public function createLinkSave(Request $request) {
        $request->validate(
            [
                'hotel_name' => 'required',
                /*'hotel_db' => 'required',
                'doc_link' => 'required',
                'business_code' => 'required',*/
            ], [
                'hotel_name.required' => '请填写酒店名称 不能为空',
                //'phone_code.required'  => '手机验证码 不能为空',
            ]
        );
        $insdata = [
            'hotel_bd'      => $request->hotel_bd,
            'hotel_name'    => $request->hotel_name,
            'doc_link'      => $request->doc_link,
            'business_code' => $request->business_code,
            'status'        => 0,
        ];
        $model   = JinjianDoc::create($insdata);
        if (!empty($model->id)) {
            return (new WidgetForm())->response()->success('创建成功')->refresh();
        }
        return (new WidgetForm())->response()->error('创建失败');
    }


    // 进件表单页
    public function index(Content $content) {
        Form::extend('exampleimg', ExampleImg::class);
        Form::extend('multipleexampleimg', MultipleExampleImg::class);
        Admin::css('/css/material-collection.css');
        admin_inject_section(Admin::SECTION['APP_INNER_BEFORE'], function () {
            $html = <<<HTML
<div class="header" style="height: 100px;margin-bottom: 20px;">
        <div class="wx-wrap">
            <div class="logo">
                <h1 class="main-logo"><a href="#">微信支付商户平台</a></h1>
                <div class="sub-logo"></div>
            </div>
        </div>
    </div>
HTML;
            return $html;
        });

        $request        = Request();
        $business_code  = $request->get('business_code');
        $role  = $request->get('role');
        $info           = JinjianDoc::where(['business_code' => $business_code])->first();
        $this->doc_info = $info;
        if (!$info) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html('<h2><i style="color:red" class="feather icon-alert-circle"></i> 未找到进件申请单信息</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('欢迎你，创建微信支付商户号')->description('')->row($form2);
        }

        if (!empty($info->status) && $info->status == 1 && empty($role)) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html('<h2><i style="color:red" class="feather icon-check-circle"></i> 进件资料已经提交，待审核</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('欢迎你，创建微信支付商户号')->description('')->row($form2);
        }

        if (!empty($info->status) && $info->status == 2) {
            $form2 = Form::make(new JinjianDoc());
            $form2->block(1, function (Form\BlockForm $form) {
            });
            $form2->block(10, function (Form\BlockForm $form) {
                $form->html("<h1>欢迎你，创建微信支付商户号</h1>");
                $form->html("<h4>申请对象: " . $this->doc_info->hotel_name . "</h4>");
                $form->html("<h5>业务申请编号: " . $this->doc_info->business_code . "</h5>");
                $form->html('<h2><i style="color:green" class="feather icon-check-circle"></i> 商户进件申请单已完成</h2>');
            });
            $form2->block(1, function (Form\BlockForm $form) {
            });
            return $content->full()->header('欢迎你，创建微信支付商户号')->description('')->row($form2);
        }

        $form = Form::make(new JinjianDoc())->edit($this->doc_info->id);
        $form->confirm('确认已经填写完整了吗？');
        $form->action('/material-collect/form/save');
        $form->block(1, function (Form\BlockForm $form) {
        });
        $form->block(10, function (Form\BlockForm $form) {
            //$form->html($html);
            $form->html("<h1>欢迎你，创建微信支付商户号</h1>");
            $form->html("<h4>申请对象: " . $this->doc_info->hotel_name . "</h4>");
            $form->html("<h5>业务申请编号: " . $this->doc_info->business_code . "</h5>");
            $role =Request()->get('role');
            if(!empty($role) && $role == 1 && $this->doc_info->status == 1){
                $form->html('<h2><i style="color:red" class="feather icon-check-circle"></i> 进件资料已经提交，待审核</h2>');
            }
            $form->next(function (Form\BlockForm $form) {
                $form->title('主体身份');
                $form->select('subject_type', '主体类型')->options([
                    'SUBJECT_TYPE_INDIVIDUAL' => '个体户',
                    'SUBJECT_TYPE_ENTERPRISE' => '企业'
                ])->value('SUBJECT_TYPE_ENTERPRISE')->width(6)->help('营业执照上的主体类型一般为有限公司、有限责任公司')->required();
                $form->hidden('finance_institution')->value(0);
                $form->hidden('business_code')->value($this->doc_info->business_code);
                $form->exampleimg('license_copy', '营业执照照片')->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d5f2c28fb232b240cfb190dfc4469227_1080x771.png')->retainable()->removable(false)->url('uploads')->uniqueName()->autoUpload()->saveFullUrl()->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d5f2c28fb232b240cfb190dfc4469227_1080x771.png" target="_blank"><b>【查看示例】</b></a>1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
<br/>2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                /*$form->text('license_number','营业执照号')->help('请依据营业执照，填写18位的统一社会信用代码')->required();
                $form->text('merchant_name','商户名称')->required();
                $form->text('legal_person','法人姓名')->required();
                $form->text('license_address','注册地址')->help('请依据营业执照/登记证书，填写注册地址')->required();
                $form->dateRange('period_begin', 'period_end', '有效期限')->required();*/
            });
            /*$form->next(function (Form\BlockForm $form) {
                $form->title('超级管理员');
                $form->date('birthday');
                $form->date('created_at');
            });*/

            $form->next(function (Form\BlockForm $form) {
                $form->title('经营者/法人身份证件');
                $form->select('id_holder_type', '证件持有人类型')->options(['LEGAL' => '经营者/法人', 'SUPER' => '经办人'])->value('LEGAL')->width(3)->required();
                $form->select('id_doc_type', '证件类型')->options(['IDENTIFICATION_TYPE_IDCARD' => '中国大陆居民-身份证'])->value('IDENTIFICATION_TYPE_IDCARD')->required();
                $form->exampleimg('id_card_copy', '法人-身份证人像面照片')->retainable()->url('uploads')->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->exampleimg('id_card_national', '法人-身份证国徽面照片')->retainable()->url('uploads')->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                /*$form->text('id_card_name','身份证姓名')->required();
                $form->text('id_card_number','身份证号码')->required();
                $form->text('id_card_address','身份证居住地址')->required();
                $form->dateRange('card_period_begin', 'card_period_end', '身份证有效期')->required();*/
            });

            $form->next(function (Form\BlockForm $form) {
                $form->title('超级管理员');
                /*$form->select('id_holder_type','证件持有人类型')->options(['LEGAL'=>'经营者/法人','SUPER'=> '经办人'])->value('LEGAL')->width(3)->required();
                $form->select('id_doc_type','证件类型')->options(['IDENTIFICATION_TYPE_IDCARD'=>'中国大陆居民-身份证'])->value('IDENTIFICATION_TYPE_IDCARD')->required();
                */
                $form->exampleimg('contact_id_doc_copy', '超管-身份证正面照片')->url('uploads')->retainable()->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->exampleimg('contact_id_card_national', '超管-身份证反面照片')->url('uploads')->retainable()->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->text('mobile_phone', '联系手机')->help('只能是手机号码')->required();
                $form->text('contact_email', '联系邮箱')->help('例：xxxxxx@qq.com 邮箱格式')->required();
                /*$form->text('id_card_name','身份证姓名')->required();
                $form->text('id_card_number','身份证号码')->required();
                $form->text('id_card_address','身份证居住地址')->required();
                $form->dateRange('card_period_begin', 'card_period_end', '身份证有效期')->required();*/
            });

            $form->next(function (Form\BlockForm $form) {
                $form->title('经营资料');
                $form->text('merchant_shortname', '商户简称')->required();
                $form->text('service_phone', '客服电话')->help('可以是手机号码或座机号码')->required();
                //$form->text('owner','所属行业')->required();
                $form->distpicker(['province_id', 'city_id', 'district_id'], '经营场所在地')->required();
                //$form->text('biz_address_code','经营场所在地')->required();
                $form->text('biz_store_address', '经营场所详细地址')->required();
                $form->exampleimg('store_entrance_pic', '经营场所门头照片')
                    ->url('uploads')
                    ->autoUpload()
                    ->retainable()
                    ->saveFullUrl()
                    ->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/42dd21b7c4678b1babb3aa17eb019058_958x537.jpeg')
                    ->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/42dd21b7c4678b1babb3aa17eb019058_958x537.jpeg" target="_blank"><b>【查看示例】</b></a> <ul><li>1.场景图片正面拍摄且清晰、完整，图片不得有遮挡</li><li>2.门店招牌清晰、招牌名称、文字可辨识、门框完整，且店面显示在营；若为停车场等无固定门头照片的经营场所，可上传岗亭/出入闸口。</li></ul>')
                    ->required();
                $form->multipleexampleimg('indoor_pic', '经营场所环境照片')
                    ->addElementClass('indoor_pic_box')
                    ->retainable()
                    ->url('uploads')
                    ->autoUpload()
                    ->saveFullUrl()
                    ->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1e1814529131d2afe86c8e55260d310b_873x466.jpeg" target="_blank"><b>【查看示例】</b></a>1.可一次选择多张上传<br.>2.请上传门店内部环境照片（可辨识经营内容）')
                    ->required();
            });

            $form->next(function (Form\BlockForm $form) {
                $form->title('结算相关<br/><span style="font-size: 12px;font-weight: 400;color:#737373">你是企业，请务必填写开户名为公司名的对公银行账户</span>');
                $form->hidden('settlement_id', '入驻结算规则ID')->value('716')->required();
                $form->hidden('qualification_type', '所属行业')->value('19')->required();
                /**
                 * BANK_ACCOUNT_TYPE_CORPORATE：对公银行账户
                 * BANK_ACCOUNT_TYPE_PERSONAL：经营者个人银行卡
                 */
                // 结算银行账户
                $bank_list = '工商银行,交通银行,招商银行,民生银行,中信银行,浦发银行,兴业银行,光大银行,广发银行,平安银行,北京银行,华夏银行,农业银行,建设银行,邮政储蓄银行,中国银行,宁波银行,其他银行';
                $form->select('bank_account_type', '账户类型')->options(['BANK_ACCOUNT_TYPE_CORPORATE' => '对公账户'])->width(3)->value('BANK_ACCOUNT_TYPE_CORPORATE')->required();
                $form->select('account_bank', '开户银行')->options(explode(',', $bank_list))->width(6)->help('城市商业银行、农村商业银行、信用合作联社及其他银行,请选择“其他银行”')->required();
                $form->text('account_name', '开户名称')->help('一般为营业执照上的企业名称')->width(3)->required();
                //$form->text('bank_address_code','开户支行')->help('如果找不到所在城市，请选择所在地区或者上级城市')->required();

                $form->distpicker(['bank_province_id', 'bank_city_id', 'bank_district_id'], '开户支行')->required();
                $form->text('account_number', '银行账号')->help('<a href="http://kf.qq.com/faq/140225MveaUz150819mYFjuE.html" target="_blank">常用银行账号位数参考</a>')->required();
                //$form->button('保存并下一步');
                $form->submitButton('保存并下一步');
            });

        });
        $form->block(1, function (Form\BlockForm $form) {

        });

        //$card1 = Card::make('', $alert->info() . $form);

        return $content->full()->header('欢迎你，创建微信支付商户号')->description('欢迎你，创建微信支付商户号')->row($form);
    }

    // 进件表单保存
    public function formSave(Request $request) {
        $validator = \Validator::make($request->all(), [
            'business_code'            => 'required',
            'license_copy'             => 'required',
            'id_card_copy'             => 'required',
            'id_card_national'         => 'required',
            'contact_id_doc_copy'      => 'required',
            'contact_id_card_national' => 'required',
            'mobile_phone'             => 'required|numeric',
            'contact_email'            => 'required|email',
            'merchant_shortname'       => 'required',
            'service_phone'            => 'required',
            'province_id'              => 'required',
            'city_id'                  => 'required',
            'district_id'              => 'required',
            //'biz_address_code'         => 'required',
            'store_entrance_pic'       => 'required',
            'indoor_pic'               => 'required',
            'bank_province_id'  => 'required',
            'bank_city_id'      => 'required',
            'bank_district_id'  => 'required',
            //'bank_address_code' => 'required',
            'account_bank'      => 'required',
            'account_name'      => 'required',
            'account_number'    => 'required|numeric',

        ], [
            'business_code.required'            => '进件业务单号 不能为空',
            'license_copy.required'             => '营业执照照片 不能为空',
            'id_card_copy.required'             => '法人-身份证人像面照片 不能为空',
            'id_card_national.required'         => '法人-身份证国徽面照片 不能为空',
            'contact_id_doc_copy.required'      => '超管-身份证人像面照片 不能为空',
            'contact_id_card_national.required' => '超管-身份证国徽面照片 不能为空',
            'mobile_phone.required'             => '超管-联系手机 不能为空',
            'mobile_phone.numeric'              => '超管-联系手机 只能是数字',
            'contact_email.required'            => '超管-联系邮箱 不能为空',
            'contact_email.email'               => '超管-联系邮箱 格式不正确',
            'merchant_shortname.required'       => '商户简称 不能为空',
            'service_phone.required'            => '客服电话 不能为空',
            'province_id.required'              => '经营场所-省份 不能为空',
            'city_id.required'                  => '经营场所-市 不能为空',
            'district_id.required'              => '经营场所-区 不能为空',
            //'biz_address_code.required'         => '经营场所-详细地址 不能为空',
            'store_entrance_pic.required'       => '经营场所门头照片 不能为空',
            'indoor_pic.required'               => '经营场所环境照片 不能为空',
            'bank_province_id.required'         => '开户支行-省份  不能为空',
            'bank_city_id.required'             => '开户支行-市  不能为空',
            'bank_district_id.required'         => '开户支行-区  不能为空',
            //'bank_address_code.required'        => '开户支行-详细地址  不能为空',
            'account_bank.required'             => '开户银行 不能为空',
            'account_name.required'             => '开户名称 不能为空',
            'account_number.required'           => '银行账号 不能为空',
            'account_number.numeric'            => '银行账号 只能是数字',
        ]);

        if ($validator->fails()) {
            return (new WidgetForm())->response()->error($validator->errors()->all()[0]);
            //returnData(204, 0, $validator->errors()->all(), '提交的数据不合法');
        }
        //info($request->all());
        $business_code = $request->get('business_code');
        $info          = JinjianDoc::where(['business_code' => $business_code])->first();
        if (!$info) {
            return (new WidgetForm())->response()->error('未找到进件业务单信息');
        }
        $store_entrance_pic = $request->store_entrance_pic;
        if (strpos($store_entrance_pic, 'http') === false) {
            $store_entrance_pic = env('APP_URL').'/jinjiandoc/' . $store_entrance_pic;
        }
        $indoor_pic_arr     = explode(',', $request->indoor_pic);
        $indoor_pic_arr_new = [];
        foreach ($indoor_pic_arr as $info) {
            $urls = $info;
            if (strpos($info, 'http') === false) {
                $urls = env('APP_URL').'/jinjiandoc/' . $urls;
            }
            $indoor_pic_arr_new[] = $urls;
        }
        $indoor_pic = implode(',', $indoor_pic_arr_new);
        $updata     = [
            'status'                   => 1,
            'subject_type'             => $request->subject_type,
            'finance_institution'      => $request->finance_institution,
            'license_copy'             => strpos($request->license_copy, 'http') === false ? env('APP_URL').'/jinjiandoc/' . $request->license_copy : $request->license_copy,
            'id_holder_type'           => $request->id_holder_type,
            'id_doc_type'              => $request->id_doc_type,
            'id_card_copy'             => strpos($request->id_card_copy, 'http') === false ? env('APP_URL').'/jinjiandoc/' . $request->id_card_copy : $request->id_card_copy,
            'id_card_national'         => strpos($request->id_card_national, 'http') === false ? env('APP_URL').'/jinjiandoc/' . $request->id_card_national : $request->id_card_national,
            'contact_id_doc_copy'      => strpos($request->contact_id_doc_copy, 'http') === false ? env('APP_URL').'/jinjiandoc/' . $request->contact_id_doc_copy : $request->contact_id_doc_copy,
            'contact_id_card_national' => strpos($request->contact_id_card_national, 'http') === false ? env('APP_URL').'/jinjiandoc/' . $request->contact_id_card_national : $request->contact_id_card_national,
            'mobile_phone'             => $request->mobile_phone,
            'contact_email'            => $request->contact_email,
            'merchant_shortname'       => $request->merchant_shortname,
            'service_phone'            => $request->service_phone,
            'province_id'              => $request->province_id,
            'city_id'                  => $request->city_id,
            'district_id'              => $request->district_id,
            'biz_address_code'         => $request->district_id,
            'biz_store_address'        => $request->biz_store_address,
            'store_entrance_pic'       => $store_entrance_pic,
            'indoor_pic'               => $indoor_pic,
            'settlement_id'            => $request->settlement_id,
            'qualification_type'       => $request->qualification_type,
            'bank_account_type'        => $request->bank_account_type,
            'bank_province_id'         => $request->bank_province_id,
            'bank_city_id'             => $request->bank_city_id,
            'bank_district_id'         => $request->bank_district_id,
            'bank_address_code'        => $request->bank_district_id,
            'account_bank'             => $request->account_bank,
            'account_name'             => $request->account_name,
            'account_number'           => $request->account_number,

            // 管理员要完善
            'license_number'           => $request->license_number,
            'merchant_name'           => $request->merchant_name,
            'legal_person'           => $request->legal_person,
            'period_begin'           => $request->period_begin,
            'period_end'           => $request->period_end,

            'id_card_name'           => $request->id_card_name,
            'id_card_number'           => $request->id_card_number,
            'id_card_address'           => $request->id_card_address,
            'card_period_begin'           => $request->card_period_begin,
            'card_period_end'           => $request->card_period_end,

            'contact_id_card_name'           => $request->contact_id_card_name,
            'contact_id_card_number'           => $request->contact_id_card_number,
            'contact_id_card_address'           => $request->contact_id_card_address,
            'contact_card_period_begin'           => $request->contact_card_period_begin,
            'contact_card_period_end'           => $request->contact_card_period_end,
        ];
        $model      = JinjianDoc::where(['business_code' => $business_code])->update($updata);
        if ($model) {
            WxRobotDoc('[商户进件] 资料已提交','商户进件资料',$business_code);
            return (new WidgetForm())->response()->success('提交成功')->refresh();
        }
        return (new WidgetForm())->response()->error('提交失败');
    }
}
