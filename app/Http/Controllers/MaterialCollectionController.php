<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Box;
use Illuminate\Contracts\Support\Renderable;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Widgets\Form as WidgetForm;
use App\Models\Hotel\Hotel;
use Dcat\Admin\Admin;
use App\Admin\Extensions\Form\UploadImgDemo;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Filter;
use SuperEggs\DcatDistpicker\Filter\DistpickerFilter;
use SuperEggs\DcatDistpicker\Form\Distpicker;
use SuperEggs\DcatDistpicker\Grid\Distpicker as GridDistpicker;
// 材料收集
class MaterialCollectionController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Content $content)
    {
        /*Admin::extend('distpicker', function () {
            Column::extend('distpicker', GridDistpicker::class);
            Form::extend('distpicker', Distpicker::class);
            Filter::extend('distpicker', DistpickerFilter::class);
        });*/
        Admin::extension()->load();
        Admin::css('/css/material-collection.css');
        $htmll = <<<HTML
<ul>
    <li>微信支付入驻</li>
</ul>
HTML;
        $alert = Alert::make($htmll, '提示:');


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
        $form =  Form::make(new Hotel());
        $form->block(1, function (Form\BlockForm $form) {
        });
        $form->block(10, function (Form\BlockForm $form) {


            //$form->html($html);
            $form->html("<h1>欢迎你，创建微信支付商户号</h1>");
            $form->html("<h5>业务申请编号: ".time()."</h5>");
            $form->next(function (Form\BlockForm $form) {
                $form->title('主体身份');
                $form->select('subject_type','主体类型')->options([
                    'SUBJECT_TYPE_INDIVIDUAL'=>'个体户',
                    'SUBJECT_TYPE_ENTERPRISE'=>'企业'
                ])->value('SUBJECT_TYPE_ENTERPRISE')->width(6)->help('营业执照上的主体类型一般为有限公司、有限责任公司')->required();
                $form->hidden('finance_institution')->value(0);
                $form->image('license_copy','营业执照照片')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d5f2c28fb232b240cfb190dfc4469227_1080x771.png" target="_blank"><b>查看示例</b></a>1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
<br/>2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->text('license_number','营业执照号')->help('请依据营业执照，填写18位的统一社会信用代码')->required();
                $form->text('merchant_name','商户名称')->required();
                $form->text('legal_person','法人姓名')->required();
                $form->text('license_address','注册地址')->help('请依据营业执照/登记证书，填写注册地址')->required();
                //$form->text('period_begin','有效期限开始日期')->required();
                $form->dateRange('period_begin', 'period_end', '有效期限')->required();
                //$form->text('period_end','有效期限结束日期')->required();
            });
            /*$form->next(function (Form\BlockForm $form) {
                $form->title('超级管理员');
                $form->date('birthday');
                $form->date('created_at');
            });*/

            /**
             * id
             * hotel_db
             * hotel_name
             * doc_link
             * subject_type 主体类型
             * finance_institution 是否金融
             * license_copy 营业执照照片
             * license_number  营业执照号
             * merchant_name  商户名称
             * legal_person  法人姓名
             * license_address  注册地址
             * period_begin 有效期限
             * period_end   有效期限
             * id_holder_type 证件持有人类型
             * id_doc_type 证件类型
             * id_card_copy 身份证人像面照片
             * id_card_national 身份证国徽面照片
             * id_card_name 身份证姓名
             * id_card_number  身份证号码
             * id_card_address 身份证居住地址
             * card_period_begin  身份证有效期
             * card_period_end 身份证有效期
             * jinjian_status
             * status
             */

            $form->next(function (Form\BlockForm $form) {
                $form->title('经营者/法人身份证件');
                $form->select('id_holder_type','证件持有人类型')->options(['LEGAL'=>'经营者/法人','SUPER'=> '经办人'])->value('LEGAL')->width(3)->required();
                $form->select('id_doc_type','证件类型')->options(['IDENTIFICATION_TYPE_IDCARD'=>'中国大陆居民-身份证'])->value('IDENTIFICATION_TYPE_IDCARD')->required();
                $form->image('id_card_copy','身份证人像面照片')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/1344340ea9fde9b9b4882c817d54282e_304x192.png" target="_blank"><b>查看示例</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->image('id_card_national','身份证国徽面照片')->help('<a href="https://gtimg.wechatpay.cn/resource/xres/img/202308/d695e67fa8440f8cb0d6f215d7871e76_296x190.png" target="_blank"><b>查看示例</b></a> 1.请上传彩色照片 or 彩色扫描件 or 加盖公章鲜章的复印件，要求正面拍摄，露出证件四角且清晰、完整，所有字符清晰可识别，不得反光或遮挡。不得翻拍、截图、镜像、PS。
2.图片只支持JPG、BMP、PNG格式，文件大小不能超过2M。')->required();
                $form->text('id_card_name','身份证姓名')->required();
                $form->text('id_card_number','身份证号码')->required();
                $form->text('id_card_address','身份证居住地址')->required();
                $form->dateRange('card_period_begin', 'card_period_end', '身份证有效期')->required();
            });

            /**
             * id
             * hotel_db
             * hotel_name
             * doc_link
             * subject_type 主体类型
             * finance_institution 是否金融
             * license_copy 营业执照照片
             * license_number  营业执照号
             * merchant_name  商户名称
             * legal_person  法人姓名
             * license_address  注册地址
             * period_begin 有效期限
             * period_end   有效期限
             * id_holder_type 证件持有人类型
             * id_doc_type 证件类型
             * id_card_copy 身份证人像面照片
             * id_card_national 身份证国徽面照片
             * id_card_name 身份证姓名
             * id_card_number  身份证号码
             * id_card_address 身份证居住地址
             * card_period_begin  身份证有效期
             * card_period_end 身份证有效期
             * merchant_shortname
             * service_phone
             * biz_address_code
             * biz_store_address
             * store_entrance_pic
             * indoor_pic
             *
             * settlement_id
             * qualification_type
             * jinjian_status
             * status
             */


            $form->next(function (Form\BlockForm $form) {
                $form->title('经营资料');
                $form->text('merchant_shortname','商户简称')->required();
                $form->text('service_phone','客服电话')->required();
                //$form->text('owner','所属行业')->required();
                $form->text('biz_address_code','经营场所在地')->required();
                $form->text('biz_store_address','经营场所详细地址')->required();
                $form->image('store_entrance_pic','经营场所门头照片')->required();
                $form->image('indoor_pic','经营场所环境照片')->required();
            });

            $form->next(function (Form\BlockForm $form) {
                $form->title('结算相关<br/><span style="font-size: 12px;font-weight: 400;color:#737373">你是企业，请务必填写开户名为“杭州铁鱼科技有限公司”的对公银行账户</span>');
                $form->hidden('settlement_id','入驻结算规则ID')->value('716')->required();
                $form->hidden('qualification_type','所属行业')->value('19')->required();

                // 结算银行账户
                $bank_list = '工商银行,交通银行,招商银行,民生银行,中信银行,浦发银行,兴业银行,光大银行,广发银行,平安银行,北京银行,华夏银行,农业银行,建设银行,邮政储蓄银行,中国银行,宁波银行,其他银行';
                $form->select('bank_account_type','账户类型')->options(['BANK_ACCOUNT_TYPE_CORPORATE'=> '对公账户'])->width(3)->value('BANK_ACCOUNT_TYPE_CORPORATE')->required();
                $form->select('account_bank','开户银行')->options(explode(',',$bank_list))->width(6)->help('城市商业银行、农村商业银行、信用合作联社及其他银行,请选择“其他银行”')->required();
                $form->text('account_name','开户名称')->help('一般为营业执照上的企业名称')->width(3)->required();
                $form->text('bank_address_code','开户支行')->help('如果找不到所在城市，请选择所在地区或者上级城市')->required();

                //$form->distpicker(['province_id', 'city_id', 'district_id'],'开户支行');
                $form->text('account_number','银行账号')->help('<a href="http://kf.qq.com/faq/140225MveaUz150819mYFjuE.html" target="_blank">常用银行账号位数参考</a>')->required();
                $form->confirm('确认已经填写完整了吗？');
                //$form->button('保存并下一步');
                $form->submitButton('保存并下一步');
            });

        });
        $form->block(1, function (Form\BlockForm $form) {

        });


        //$card1 = Card::make('', $alert->info() . $form);

        return $content->full()->header('欢迎你，创建微信支付商户号')->description('欢迎你，创建微信支付商户号')->row($form);
    }
}
