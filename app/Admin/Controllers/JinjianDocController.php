<?php
/**
 * @copyright ©2023 杨光
 * @author 杨光
 * @link https://www.saishiyun.net/
 * @contact wx:Q3664839
 * Created by Phpstorm
 * 学习永无止镜 践行开源公益
 */
 
namespace App\Admin\Controllers;

use App\Models\Hotel\JinjianDoc;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use App\Admin\Extensions\Form\ExampleImg;
use App\Admin\Extensions\Form\MultipleExampleImg;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Alert;

// 列表
class JinjianDocController extends AdminController
{

    /**
     * page index
     */
    public function index(Content $content)
    {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text'=>'列表','uri'=>''])
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
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
        $grid =  Grid::make(new JinjianDoc(), function (Grid $grid) {
            $grid->model()->orderBy('id','DESC');
            $grid->column('id');
            $grid->column('business_code')->display(function ($business_code){
                return "<a target='_blank' href='" . $this->doc_link . "&role=1'>$business_code</a>&nbsp;&nbsp;&nbsp;&nbsp;<span class='clipboard-txt' data-clipboard-text='" . $this->doc_link . "'> <i class='feather icon-copy'></i></span>";
            });
            $grid->column('hotel_name');
            $grid->column('mch_id','微信商户号')->editable();

            // 传入字符串
            $form = Form::make(new JinjianDoc());
            $form->confirm('确认现在生成吗？');
            $form->action('/material-collect/create-link/save');
            $business_code = '1566291601_' . time();
            $form->title('创建商户进件链接<br/><span style="font-size: 12px;font-weight: 400;color:#737373">每个链接唯一使用</span>');
            $form->text('hotel_name', '酒店名称')->required();
            $form->hidden('doc_link', '酒店名称')->value(url('admin/material-collect/form?business_code=' . $business_code))->required();
            $form->hidden('business_code', '酒店名称')->value($business_code)->required();
            $form->hidden('hotel_bd', '业务BD')->value('杨总');
            $form->disableEditingCheck();
            $form->disableCreatingCheck();
            $form->disableViewCheck();
            $modal = Modal::make()
                ->lg()
                ->title('生成进件链接')
                ->body($form)
                ->button('<button class="btn btn-primary">生成进件链接</button>');
            $grid->tools($modal);
            $grid->column('status')->select(JinjianDoc::Status_arr, true);
            $grid->column('created_at');
            $grid->quickSearch(['hotel_name','business_code','mch_id'])->placeholder('酒店名,进件业务编号,商户号');
            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->actions(function ($actions) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableView();
            });
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });

        $alert = Alert::make('<ul><li>可以在手机端使用: <a target="_blank" href="'.url('/admin/material-collect/create-link').'" >查看</a> &nbsp;&nbsp;<span class="clipboard-txt tips" data-title="点击及可复制" data-clipboard-text="'.url('ycookies/jinjian/create-link').'"> <i class="feather icon-copy"></i> </span> </li></ul>', '使用说明');
        $alert->info();
        $alert->icon('feather icon-alert-triangle');
        $alert->removable();
        return $alert.$grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new JinjianDoc(), function (Show $show) {
            $show->field('id');
            $show->field('hotel_bd');
            $show->field('hotel_name');
            $show->field('doc_link');
            $show->field('business_code');
            $show->field('subject_type');
            $show->field('finance_institution');
            $show->field('license_copy');
            $show->field('license_number');
            $show->field('merchant_name');
            $show->field('legal_person');
            $show->field('license_address');
            $show->field('period_begin');
            $show->field('period_end');
            $show->field('id_holder_type');
            $show->field('id_doc_type');
            $show->field('id_card_copy');
            $show->field('id_card_national');
            $show->field('id_card_name');
            $show->field('id_card_number');
            $show->field('id_card_address');
            $show->field('card_period_begin');
            $show->field('card_period_end');
            $show->field('contact_id_doc_copy');
            $show->field('contact_id_card_national');
            $show->field('mobile_phone');
            $show->field('contact_email');
            $show->field('merchant_shortname');
            $show->field('service_phone');
            $show->field('province_id');
            $show->field('city_id');
            $show->field('district_id');
            $show->field('biz_address_code');
            $show->field('biz_store_address');
            $show->field('store_entrance_pic');
            $show->field('indoor_pic');
            $show->field('settlement_id');
            $show->field('qualification_type');
            $show->field('bank_account_type');
            $show->field('account_bank');
            $show->field('account_name');
            $show->field('bank_province_id');
            $show->field('bank_city_id');
            $show->field('bank_district_id');
            $show->field('bank_address_code');
            $show->field('account_number');
            $show->field('jinjian_status');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Form::extend('exampleimg', ExampleImg::class);
        Form::extend('multipleexampleimg', MultipleExampleImg::class);
        Admin::css('/css/material-collection.css');

        return Form::make(new JinjianDoc(), function (Form $form) {
            $form->confirm('确认已经填写完整了吗？');
            $form->action('/material-collect/form/save');
            $form->block(1, function (Form\BlockForm $form) {
            });
            $form->block(10, function (Form\BlockForm $form) {
                //$form->html($html);
                $form->html("<h4>申请对象: " . $form->model()->hotel_name. "</h4>");
                $form->html("<h5>业务申请编号: " . $form->model()->business_code . "</h5>");
                $form->next(function (Form\BlockForm $form) {
                    $form->title('主体身份');
                    $form->select('subject_type', '主体类型')->options([
                        'SUBJECT_TYPE_INDIVIDUAL' => '个体户',
                        'SUBJECT_TYPE_ENTERPRISE' => '企业'
                    ])->value('SUBJECT_TYPE_ENTERPRISE')->width(6)->help('营业执照上的主体类型一般为有限公司、有限责任公司')->required();
                    $form->hidden('finance_institution')->value(0);

                    $form->hidden('business_code')->value($form->model()->business_code);
                    $form->exampleimg('license_copy', '营业执照照片')->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d5f2c28fb232b240cfb190dfc4469227_1080x771.png')->retainable()->removable(false)->url('uploads')->uniqueName()->autoUpload()->saveFullUrl()->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d5f2c28fb232b240cfb190dfc4469227_1080x771.png" target="_blank"><b>【查看示例】</b></a>1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
<br/>2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                    $form->text('license_number','营业执照号')->help('请依据营业执照，填写18位的统一社会信用代码');
                    $form->text('merchant_name','商户名称')->help('营业执照登记名称');
                    $form->text('legal_person','法人姓名');
                    $form->text('license_address','注册地址')->help('请依据营业执照/登记证书，填写注册地址');
                    $form->dateRange('period_begin', 'period_end', '有效期限');
                });
                $form->next(function (Form\BlockForm $form) {
                    $form->title('经营者/法人身份证件');
                    $form->select('id_holder_type', '证件持有人类型')->options(['LEGAL' => '经营者/法人', 'SUPER' => '经办人'])->value('LEGAL')->width(3)->required();
                    $form->select('id_doc_type', '证件类型')->options(['IDENTIFICATION_TYPE_IDCARD' => '中国大陆居民-身份证'])->value('IDENTIFICATION_TYPE_IDCARD')->required();
                    $form->exampleimg('id_card_copy', '法人-身份证人像面照片')->retainable()->url('uploads')->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                    $form->exampleimg('id_card_national', '法人-身份证国徽面照片')->retainable()->url('uploads')->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                    $form->text('id_card_name','身份证姓名');
                    $form->text('id_card_number','身份证号码');
                    $form->text('id_card_address','身份证居住地址');
                    $form->dateRange('card_period_begin', 'card_period_end', '身份证有效期');
                });

                $form->next(function (Form\BlockForm $form) {
                    $form->title('超级管理员');
                    $form->select('id_holder_type','证件持有人类型')->options(['LEGAL'=>'经营者/法人','SUPER'=> '经办人'])->value('LEGAL')->width(3)->required();
                    $form->select('id_doc_type','证件类型')->options(['IDENTIFICATION_TYPE_IDCARD'=>'中国大陆居民-身份证'])->value('IDENTIFICATION_TYPE_IDCARD')->required();
                    $form->exampleimg('contact_id_doc_copy', '超管-身份证正面照片')->url('uploads')->retainable()->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                    $form->exampleimg('contact_id_card_national', '超管-身份证反面照片')->url('uploads')->retainable()->autoUpload()->saveFullUrl()->updemoimg('https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png" target="_blank"><b>【查看示例】</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                    $form->text('mobile_phone', '联系手机')->help('只能是手机号码')->required();
                    $form->text('contact_email', '联系邮箱')->help('例：3664839@qq.com 邮箱格式')->required();
                    $form->text('contact_id_card_name','身份证姓名');
                    $form->text('contact_id_card_number','身份证号码');
                    $form->text('contact_id_card_address','身份证居住地址');
                    $form->dateRange('contact_card_period_begin', 'card_period_end', '身份证有效期');
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
                    $form->title('结算相关<br/><span style="font-size: 12px;font-weight: 400;color:#737373">你是企业，请务必填写开户名为“杭州铁鱼科技有限公司”的对公银行账户</span>');
                    $form->hidden('settlement_id', '入驻结算规则ID')->value('716')->required();
                    $form->hidden('qualification_type', '所属行业')->value('19');
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

                    $form->hidden('status');
                    $form->submitButton('保存并下一步');
                });

            });
            $form->block(1, function (Form\BlockForm $form) {

            });
        });
    }
}
